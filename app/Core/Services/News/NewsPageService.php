<?php

namespace App\Core\Services\News;

use App\Core\Repositories\Eloquent\HomepageRepository;
use App\Core\Services\Upload\UploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class NewsPageService
{
    public function __construct(
        private HomepageRepository $homepageRepository,
        private UploadService $uploadService
    ) {}

    public function getNewsPage(): array
    {
        $data = $this->homepageRepository->getSection('news_page');

        if (!is_array($data)) {
            $data = [];
        }

        return [
            'banner' => $data['banner'] ?? [
                'breadcrumb_first' => 'Home',
                'breadcrumb_first_url' => '/',
                'breadcrumb_second' => 'News',
                'breadcrumb_second_url' => '/news',
                'title' => 'The YC Events',
                'description' => '',
                'background_image' => null,
            ],

            'newsletter' => $data['newsletter'] ?? [
                'title' => '',
                'description' => '',
                'inputPlaceholder' => '',
                'buttonText' => '',
                'buttonUrl' => '',
            ],

            'postCategories' => $this->normalizeCategories(
                $data['categories'] ?? []
            ),

            'latest' => $this->normalizeItems(
                $data['latest'] ?? [],
                false
            ),

            'featured' => $this->normalizeItems(
                $data['featured'] ?? [],
                true
            ),
        ];
    }

    private function normalizeCategories(mixed $categories): array
    {
        if (!is_array($categories)) {
            return [];
        }

        return collect($categories)
            ->filter(fn($category) => is_array($category))
            ->map(function ($category) {
                return [
                    'id' => !empty($category['id'])
                        ? (string) $category['id']
                        : (string) Str::uuid(),

                    'title' => $category['title'] ?? '',

                    'description' => $category['description'] ?? '',
                ];
            })
            ->values()
            ->toArray();
    }

    private function normalizeItems(
        mixed $items,
        bool $featured
    ): array {
        if (!is_array($items)) {
            return [];
        }

        return collect($items)
            ->filter(fn($item) => is_array($item))
            ->map(function ($item) use ($featured) {
                $item['id'] = !empty($item['id'])
                    ? (string) $item['id']
                    : (string) Str::uuid();

                $item['title'] = $item['title'] ?? '';
                $item['description'] = $item['description'] ?? '';
                $item['category'] = $item['category'] ?? '';
                $item['date'] = $item['date'] ?? '';
                $item['status'] = $item['status'] ?? 'Draft';
                $item['featured'] = $featured;
                $item['image'] = $item['image'] ?? null;

                return $item;
            })
            ->values()
            ->toArray();
    }

    /**
     * Update categories, banner, newsletter.
     */
    public function updateSection(
        string $collection,
        array $input,
        ?UploadedFile $image = null
    ): array {
        $data = $this->homepageRepository->getSection('news_page');

        if (!is_array($data)) {
            $data = [];
        }

        /*
        |--------------------------------------------------------------------------
        | Categories
        |--------------------------------------------------------------------------
        */

        if ($collection === 'categories') {
            $categories = $data['categories'] ?? [];

            if (!is_array($categories)) {
                $categories = [];
            }

            $input['title'] = trim(
                (string) ($input['title'] ?? '')
            );

            $input['description'] = trim(
                (string) ($input['description'] ?? '')
            );

            if ($input['title'] === '') {
                throw new \InvalidArgumentException(
                    'Category title is required.'
                );
            }

            if ($input['description'] === '') {
                throw new \InvalidArgumentException(
                    'Category description is required.'
                );
            }

            if (empty($input['id'])) {
                $input['id'] = (string) Str::uuid();

                $categories[] = $input;
            } else {
                $input['id'] = (string) $input['id'];

                $updated = false;

                foreach ($categories as $index => $category) {
                    if (
                        is_array($category) &&
                        isset($category['id']) &&
                        (string) $category['id'] === (string) $input['id']
                    ) {
                        $categories[$index] = array_merge(
                            $category,
                            $input
                        );

                        $updated = true;

                        break;
                    }
                }

                if (!$updated) {
                    $categories[] = $input;
                }
            }

            $data['categories'] = array_values($categories);
        }

        /*
        |--------------------------------------------------------------------------
        | Banner
        |--------------------------------------------------------------------------
        */ elseif ($collection === 'banner') {
            $oldBanner = $data['banner'] ?? [];

            if (!is_array($oldBanner)) {
                $oldBanner = [];
            }

            /*
            |--------------------------------------------------------------------------
            | Có upload ảnh mới
            |--------------------------------------------------------------------------
            */

            if ($image instanceof UploadedFile) {

                // Xóa ảnh banner cũ trên Cloudinary
                if (
                    isset($oldBanner['background_image']['public_id']) &&
                    !empty($oldBanner['background_image']['public_id'])
                ) {
                    $this->uploadService->deleteImage(
                        $oldBanner['background_image']['public_id']
                    );
                }

                // Upload ảnh mới
                $uploadedImage = $this->uploadService->uploadImage(
                    $image,
                    'news/page/banner'
                );

                $input['background_image'] = [
                    'url' => $uploadedImage['url'] ?? null,
                    'public_id' => $uploadedImage['public_id'] ?? null,
                ];
            }

            /*
            |--------------------------------------------------------------------------
            | Không upload ảnh mới
            | Giữ nguyên ảnh cũ
            |--------------------------------------------------------------------------
            */ else {
                if (isset($oldBanner['background_image'])) {
                    $input['background_image'] =
                        $oldBanner['background_image'];
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Merge banner cũ + dữ liệu mới
            |--------------------------------------------------------------------------
            */

            $data['banner'] = array_merge(
                $oldBanner,
                $input
            );
        }
 elseif ($collection === 'newsletter') {
            $oldNewsletter = $data['newsletter'] ?? [];

            if (!is_array($oldNewsletter)) {
                $oldNewsletter = [];
            }

            $data['newsletter'] = array_merge(
                $oldNewsletter,
                $input
            );
        } else {
            throw new \InvalidArgumentException(
                "Invalid News Page section: {$collection}"
            );
        }

        $this->homepageRepository->updateSection(
            'news_page',
            $data
        );

        return $data;
    }
    public function updateItem(
        string $collection,
        array $input,
        ?UploadedFile $image = null
    ): array {
        if (!in_array($collection, ['latest', 'featured'], true)) {
            throw new \InvalidArgumentException(
                "Invalid News Page collection: {$collection}"
            );
        }

        $data = $this->homepageRepository->getSection('news_page');

        if (!is_array($data)) {
            $data = [];
        }

        $items = $data[$collection] ?? [];

        if (!is_array($items)) {
            $items = [];
        }

        if (empty($input['id'])) {
            $input['id'] = (string) Str::uuid();
        } else {
            $input['id'] = (string) $input['id'];
        }

        $input['title'] = $input['title'] ?? '';
        $input['description'] = $input['description'] ?? '';
        $input['category'] = $input['category'] ?? '';
        $input['date'] = $input['date'] ?? '';
        $input['status'] = $input['status'] ?? 'Draft';
        $input['featured'] = $collection === 'featured';

        if ($image instanceof UploadedFile) {
            $uploadedImage = $this->uploadService->uploadImage(
                $image,
                'news/page'
            );

            $input['image'] = [
                'url' => $uploadedImage['url'] ?? null,
                'public_id' => $uploadedImage['public_id'] ?? null,
            ];
        }

        $existingIndex = null;

        foreach ($items as $index => $item) {
            if (
                is_array($item) &&
                isset($item['id']) &&
                (string) $item['id'] === (string) $input['id']
            ) {
                $existingIndex = $index;
                break;
            }
        }

        if ($existingIndex === null) {
            $input['image'] = $input['image'] ?? null;

            $items[] = $input;
        } else {
            $oldItem = $items[$existingIndex];

            if (
                !isset($input['image']) &&
                isset($oldItem['image'])
            ) {
                $input['image'] = $oldItem['image'];
            }

            $items[$existingIndex] = array_merge(
                $oldItem,
                $input
            );
        }

        $data[$collection] = array_values($items);

        $this->homepageRepository->updateSection(
            'news_page',
            $data
        );

        return $input;
    }

    public function deleteItem(
        string $collection,
        string $id
    ): bool {
        if ($collection === 'categories') {
            return $this->deleteCategory($id);
        }

        if (!in_array($collection, ['latest', 'featured'], true)) {
            return false;
        }

        $data = $this->homepageRepository->getSection('news_page');

        if (!is_array($data)) {
            return false;
        }

        $items = $data[$collection] ?? [];

        if (!is_array($items)) {
            return false;
        }

        $item = collect($items)->first(
            fn($item) =>
            is_array($item) &&
                isset($item['id']) &&
                (string) $item['id'] === (string) $id
        );

        if (!$item) {
            return false;
        }

        if (
            isset($item['image']['public_id']) &&
            !empty($item['image']['public_id'])
        ) {
            $this->uploadService->deleteImage(
                $item['image']['public_id']
            );
        }

        $data[$collection] = collect($items)
            ->reject(
                fn($item) =>
                is_array($item) &&
                    isset($item['id']) &&
                    (string) $item['id'] === (string) $id
            )
            ->values()
            ->toArray();

        $this->homepageRepository->updateSection(
            'news_page',
            $data
        );

        return true;
    }

    private function deleteCategory(string $id): bool
    {
        $data = $this->homepageRepository->getSection('news_page');

        if (!is_array($data)) {
            return false;
        }

        $categories = $data['categories'] ?? [];

        if (!is_array($categories)) {
            return false;
        }

        $exists = collect($categories)->contains(
            fn($category) =>
            is_array($category) &&
                isset($category['id']) &&
                (string) $category['id'] === (string) $id
        );

        if (!$exists) {
            return false;
        }

        $data['categories'] = collect($categories)
            ->reject(
                fn($category) =>
                is_array($category) &&
                    isset($category['id']) &&
                    (string) $category['id'] === (string) $id
            )
            ->values()
            ->toArray();

        $this->homepageRepository->updateSection(
            'news_page',
            $data
        );

        return true;
    }
}
