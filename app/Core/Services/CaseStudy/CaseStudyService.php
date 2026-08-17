<?php

namespace App\Core\Services\CaseStudy;

use App\Core\Repositories\Eloquent\HomepageRepository;
use App\Core\Services\Upload\UploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CaseStudyService
{
    public function __construct(
        private HomepageRepository $homepageRepository,
        private UploadService $uploadService
    ) {}

    public function getCaseStudyPage(): array
    {
        $data = $this->homepageRepository->getSection('case_study_page');

        if (!is_array($data)) {
            $data = [];
        }

        return [
            'banner' => $this->normalizeBanner(
                $data['banner'] ?? []
            ),
            'categories' => $this->normalizeCategories(
                $data['categories'] ?? []
            ),
            'caseStudies' => $this->normalizeItems(
                $data['caseStudies'] ?? []
            ),
        ];
    }

    private function normalizeBanner(mixed $banner): array
    {
        if (!is_array($banner)) {
            $banner = [];
        }

        return [
            'breadcrumb_first' => trim(
                (string) ($banner['breadcrumb_first'] ?? '')
            ),
            'breadcrumb_first_url' => trim(
                (string) ($banner['breadcrumb_first_url'] ?? '')
            ),
            'breadcrumb_second' => trim(
                (string) ($banner['breadcrumb_second'] ?? '')
            ),
            'breadcrumb_second_url' => trim(
                (string) ($banner['breadcrumb_second_url'] ?? '')
            ),
            'title' => trim(
                (string) ($banner['title'] ?? '')
            ),
            'description' => trim(
                (string) ($banner['description'] ?? '')
            ),
            'background_image' => $banner['background_image'] ?? null,
        ];
    }

    private function normalizeCategories(mixed $items): array
    {
        if (!is_array($items)) {
            return [];
        }

        return collect($items)
            ->filter(fn($item) => is_array($item))
            ->map(function ($item) {
                $item['id'] = !empty($item['id'])
                    ? (string) $item['id']
                    : (string) Str::uuid();

                $item['title'] = trim(
                    (string) ($item['title'] ?? '')
                );

                $children = $item['children'] ?? [];

                if (!is_array($children)) {
                    $children = [];
                }

                $item['children'] = collect($children)
                    ->filter(fn($child) => is_array($child))
                    ->map(function ($child) {
                        $name = trim(
                            (string) ($child['name'] ?? '')
                        );

                        if ($name === '') {
                            return null;
                        }

                        return [
                            'id' => !empty($child['id'])
                                ? (string) $child['id']
                                : (string) Str::uuid(),
                            'name' => $name,
                        ];
                    })
                    ->filter()
                    ->values()
                    ->toArray();

                return $item;
            })
            ->filter(
                fn($item) => $item['title'] !== ''
            )
            ->values()
            ->toArray();
    }

    private function normalizeItems(mixed $items): array
    {
        if (!is_array($items)) {
            return [];
        }

        return collect($items)
            ->filter(fn($item) => is_array($item))
            ->map(function ($item) {
                $item['id'] = !empty($item['id'])
                    ? (string) $item['id']
                    : (string) Str::uuid();

                $item['title'] = $item['title'] ?? '';
                $item['description'] = $item['description'] ?? '';

                $item['categories'] = is_array(
                    $item['categories'] ?? null
                )
                    ? array_values($item['categories'])
                    : [];

                $item['active'] = $item['active'] ?? true;
                $item['image'] = $item['image'] ?? null;
                $item['logo'] = $item['logo'] ?? null;

                return $item;
            })
            ->values()
            ->toArray();
    }

    public function updateItem(
        string $collection,
        array $input,
        ?UploadedFile $image = null,
        ?UploadedFile $logo = null
    ): array {
        if (!in_array(
            $collection,
            ['banner', 'categories', 'caseStudies'],
            true
        )) {
            throw new \InvalidArgumentException(
                "Invalid Case Study collection: {$collection}"
            );
        }

        $data = $this->homepageRepository->getSection(
            'case_study_page'
        );

        if (!is_array($data)) {
            $data = [];
        }

        return match ($collection) {
            'banner' => $this->updateBanner(
                $data,
                $input,
                $image
            ),

            'categories' => $this->updateCategory(
                $data,
                $input
            ),

            'caseStudies' => $this->updateCaseStudy(
                $data,
                $input,
                $image,
                $logo
            ),
        };
    }

    private function updateBanner(
        array $data,
        array $input,
        ?UploadedFile $image = null
    ): array {
        $oldBanner = $data['banner'] ?? [];

        if (!is_array($oldBanner)) {
            $oldBanner = [];
        }

        $banner = [
            'breadcrumb_first' => trim(
                (string) ($input['breadcrumb_first'] ?? '')
            ),
            'breadcrumb_first_url' => trim(
                (string) ($input['breadcrumb_first_url'] ?? '')
            ),
            'breadcrumb_second' => trim(
                (string) ($input['breadcrumb_second'] ?? '')
            ),
            'breadcrumb_second_url' => trim(
                (string) ($input['breadcrumb_second_url'] ?? '')
            ),
            'title' => trim(
                (string) ($input['title'] ?? '')
            ),
            'description' => trim(
                (string) ($input['description'] ?? '')
            ),
        ];

        if ($banner['title'] === '') {
            throw new \InvalidArgumentException(
                'Case Study banner title is required.'
            );
        }

        if ($banner['description'] === '') {
            throw new \InvalidArgumentException(
                'Case Study banner description is required.'
            );
        }

        if ($image instanceof UploadedFile) {
            $uploadedImage = $this->uploadService->uploadImage(
                $image,
                'case-study/banner'
            );

            $banner['background_image'] = [
                'url' => $uploadedImage['url'] ?? null,
                'public_id' => $uploadedImage['public_id'] ?? null,
            ];

            $oldPublicId =
                $oldBanner['background_image']['public_id'] ?? null;

            if (
                $oldPublicId &&
                $oldPublicId !==
                ($banner['background_image']['public_id'] ?? null)
            ) {
                $this->uploadService->deleteImage(
                    $oldPublicId
                );
            }
        } else {
            $banner['background_image'] =
                $oldBanner['background_image'] ?? null;
        }

        $data['banner'] = $banner;

        $this->homepageRepository->updateSection(
            'case_study_page',
            $data
        );

        $savedData = $this->homepageRepository->getSection(
            'case_study_page'
        );

        if (!is_array($savedData)) {
            $savedData = $data;
        }

        return $this->normalizeBanner(
            $savedData['banner'] ?? $banner
        );
    }

    private function updateCategory(
        array $data,
        array $input
    ): array {
        $categories = $data['categories'] ?? [];

        if (!is_array($categories)) {
            $categories = [];
        }

        $categoryId = !empty($input['id'])
            ? (string) $input['id']
            : (string) Str::uuid();

        $title = trim(
            (string) ($input['title'] ?? '')
        );

        if ($title === '') {
            throw new \InvalidArgumentException(
                'Category title is required.'
            );
        }

        $children = $input['children'] ?? [];

        if (!is_array($children)) {
            $children = [];
        }

        $normalizedChildren = [];

        foreach ($children as $child) {
            if (!is_array($child)) {
                continue;
            }

            $name = trim(
                (string) ($child['name'] ?? '')
            );

            if ($name === '') {
                continue;
            }

            $childId = !empty($child['id'])
                ? (string) $child['id']
                : (string) Str::uuid();

            $normalizedChildren[] = [
                'id' => $childId,
                'name' => $name,
            ];
        }

        $updatedCategory = [
            'id' => $categoryId,
            'title' => $title,
            'children' => $normalizedChildren,
        ];

        $existingIndex = null;

        foreach ($categories as $index => $category) {
            if (
                is_array($category) &&
                isset($category['id']) &&
                (string) $category['id'] === $categoryId
            ) {
                $existingIndex = $index;
                break;
            }
        }

        if ($existingIndex === null) {
            $categories[] = $updatedCategory;
        } else {
            $categories[$existingIndex] = $updatedCategory;
        }

        $data['categories'] = array_values($categories);

        $this->homepageRepository->updateSection(
            'case_study_page',
            $data
        );

        $savedData = $this->homepageRepository->getSection(
            'case_study_page'
        );

        if (!is_array($savedData)) {
            $savedData = $data;
        }

        $savedCategories = $this->normalizeCategories(
            $savedData['categories'] ?? []
        );

        foreach ($savedCategories as $savedCategory) {
            if (
                isset($savedCategory['id']) &&
                (string) $savedCategory['id'] === $categoryId
            ) {
                return $savedCategory;
            }
        }

        return $updatedCategory;
    }

    private function updateCaseStudy(
        array $data,
        array $input,
        ?UploadedFile $image = null,
        ?UploadedFile $logo = null
    ): array {
        $items = $data['caseStudies'] ?? [];

        if (!is_array($items)) {
            $items = [];
        }

        $input['id'] = !empty($input['id'])
            ? (string) $input['id']
            : (string) Str::uuid();

        $input['title'] = trim(
            (string) ($input['title'] ?? '')
        );

        $input['description'] = trim(
            (string) ($input['description'] ?? '')
        );

        $input['categories'] = is_array(
            $input['categories'] ?? null
        )
            ? array_values(
                array_filter(
                    array_map(
                        fn($category) => trim((string) $category),
                        $input['categories']
                    ),
                    fn($category) => $category !== ''
                )
            )
            : [];

        if (count($input['categories']) > 2) {
            throw new \InvalidArgumentException(
                'A Case Study can have a maximum of 2 categories.'
            );
        }

        $input['active'] = filter_var(
            $input['active'] ?? true,
            FILTER_VALIDATE_BOOLEAN,
            FILTER_NULL_ON_FAILURE
        );

        if ($input['active'] === null) {
            $input['active'] = true;
        }

        if ($input['title'] === '') {
            throw new \InvalidArgumentException(
                'Case Study title is required.'
            );
        }

        if ($input['description'] === '') {
            throw new \InvalidArgumentException(
                'Case Study description is required.'
            );
        }

        if (empty($input['categories'])) {
            throw new \InvalidArgumentException(
                'At least one Case Study category is required.'
            );
        }

        $existingIndex = null;
        $oldItem = null;

        foreach ($items as $index => $item) {
            if (
                is_array($item) &&
                isset($item['id']) &&
                (string) $item['id'] ===
                (string) $input['id']
            ) {
                $existingIndex = $index;
                $oldItem = $item;
                break;
            }
        }

        if ($image instanceof UploadedFile) {
            $uploadedImage = $this->uploadService->uploadImage(
                $image,
                'case-study'
            );

            $input['image'] = [
                'url' => $uploadedImage['url'] ?? null,
                'public_id' => $uploadedImage['public_id'] ?? null,
            ];
        } elseif ($oldItem !== null) {
            $input['image'] = $oldItem['image'] ?? null;
        } else {
            $input['image'] = null;
        }

        if ($logo instanceof UploadedFile) {
            $uploadedLogo = $this->uploadService->uploadImage(
                $logo,
                'case-study/logo'
            );

            $input['logo'] = [
                'url' => $uploadedLogo['url'] ?? null,
                'public_id' => $uploadedLogo['public_id'] ?? null,
            ];
        } elseif ($oldItem !== null) {
            $input['logo'] = $oldItem['logo'] ?? null;
        } else {
            $input['logo'] = null;
        }

        if ($existingIndex === null) {
            $items[] = $input;
        } else {
            if (
                isset($oldItem['image']['public_id']) &&
                !empty($oldItem['image']['public_id']) &&
                isset($input['image']['public_id']) &&
                !empty($input['image']['public_id']) &&
                $oldItem['image']['public_id'] !==
                $input['image']['public_id']
            ) {
                $this->uploadService->deleteImage(
                    $oldItem['image']['public_id']
                );
            }

            if (
                isset($oldItem['logo']['public_id']) &&
                !empty($oldItem['logo']['public_id']) &&
                isset($input['logo']['public_id']) &&
                !empty($input['logo']['public_id']) &&
                $oldItem['logo']['public_id'] !==
                $input['logo']['public_id']
            ) {
                $this->uploadService->deleteImage(
                    $oldItem['logo']['public_id']
                );
            }

            $items[$existingIndex] = array_merge(
                $oldItem,
                $input
            );
        }

        $data['caseStudies'] = array_values($items);

        $this->homepageRepository->updateSection(
            'case_study_page',
            $data
        );

        $savedData = $this->homepageRepository->getSection(
            'case_study_page'
        );

        if (!is_array($savedData)) {
            $savedData = $data;
        }

        $savedItems = $this->normalizeItems(
            $savedData['caseStudies'] ?? []
        );

        foreach ($savedItems as $savedItem) {
            if (
                isset($savedItem['id']) &&
                (string) $savedItem['id'] ===
                (string) $input['id']
            ) {
                return $savedItem;
            }
        }

        return $input;
    }

    public function deleteItem(
        string $collection,
        string $id
    ): bool {
        if (!in_array(
            $collection,
            ['categories', 'caseStudies'],
            true
        )) {
            return false;
        }

        $data = $this->homepageRepository->getSection(
            'case_study_page'
        );

        if (!is_array($data)) {
            return false;
        }

        if ($collection === 'categories') {
            return $this->deleteCategory(
                $data,
                $id
            );
        }

        return $this->deleteCaseStudyItem(
            $data,
            $id
        );
    }

    private function deleteCategory(
        array $data,
        string $id
    ): bool {
        $categories = $data['categories'] ?? [];

        if (!is_array($categories)) {
            return false;
        }

        $found = false;

        $categories = collect($categories)
            ->filter(function ($category) use ($id, &$found) {
                if (
                    is_array($category) &&
                    isset($category['id']) &&
                    (string) $category['id'] ===
                    (string) $id
                ) {
                    $found = true;
                    return false;
                }

                return true;
            })
            ->values()
            ->toArray();

        if (!$found) {
            return false;
        }

        $data['categories'] = $categories;

        $this->homepageRepository->updateSection(
            'case_study_page',
            $data
        );

        return true;
    }

    private function deleteCaseStudyItem(
        array $data,
        string $id
    ): bool {
        $items = $data['caseStudies'] ?? [];

        if (!is_array($items)) {
            return false;
        }

        $item = collect($items)->first(
            fn($item) =>
            is_array($item) &&
                isset($item['id']) &&
                (string) $item['id'] ===
                (string) $id
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

        if (
            isset($item['logo']['public_id']) &&
            !empty($item['logo']['public_id'])
        ) {
            $this->uploadService->deleteImage(
                $item['logo']['public_id']
            );
        }

        $data['caseStudies'] = collect($items)
            ->reject(
                fn($item) =>
                is_array($item) &&
                    isset($item['id']) &&
                    (string) $item['id'] ===
                    (string) $id
            )
            ->values()
            ->toArray();

        $this->homepageRepository->updateSection(
            'case_study_page',
            $data
        );

        return true;
    }

    public function deleteImage(
        string $id,
        string $collection = 'caseStudies'
    ): bool {
        $data = $this->homepageRepository->getSection(
            'case_study_page'
        );

        if (!is_array($data)) {
            return false;
        }

        if ($collection === 'banner') {
            $banner = $data['banner'] ?? [];

            if (!is_array($banner)) {
                return false;
            }

            $publicId =
                $banner['background_image']['public_id'] ?? null;

            if ($publicId) {
                $this->uploadService->deleteImage($publicId);
            }

            $data['banner']['background_image'] = null;

            $this->homepageRepository->updateSection(
                'case_study_page',
                $data
            );

            return true;
        }

        $items = $data['caseStudies'] ?? [];

        if (!is_array($items)) {
            return false;
        }

        foreach ($items as $index => $item) {
            if (
                !is_array($item) ||
                !isset($item['id']) ||
                (string) $item['id'] !==
                (string) $id
            ) {
                continue;
            }

            if (
                isset($item['image']['public_id']) &&
                !empty($item['image']['public_id'])
            ) {
                $this->uploadService->deleteImage(
                    $item['image']['public_id']
                );
            }

            $items[$index]['image'] = null;

            $data['caseStudies'] = array_values($items);

            $this->homepageRepository->updateSection(
                'case_study_page',
                $data
            );

            return true;
        }

        return false;
    }
}
