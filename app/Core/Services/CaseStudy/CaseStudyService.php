<?php

namespace App\Core\Services\CaseStudy;

use App\Core\Repositories\Eloquent\HomepageRepository;
use App\Core\Services\Upload\UploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class CaseStudyService
{
    public function __construct(
        private HomepageRepository $homepageRepository,
        private UploadService $uploadService
    ) {}

    public function getCaseStudyPage(): array
    {
        $data = $this->homepageRepository->getSection(
            'case_study_page'
        );

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
            'caseStudyDetail' => $this->normalizeCaseStudyDetail(
                $data['caseStudyDetail'] ?? []
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

    private function normalizeCaseStudyDetail(mixed $detail): array
    {
        if (!is_array($detail)) {
            $detail = [];
        }

        $socialMedia = $detail['social_media'] ?? [];

        if (!is_array($socialMedia)) {
            $socialMedia = [];
        }

        $socialMedia = collect($socialMedia)
            ->filter(fn($item) => is_array($item))
            ->map(function ($item) {
                $icon = $item['icon'] ?? [];

                if (is_string($icon)) {
                    $decodedIcon = json_decode(
                        $icon,
                        true
                    );

                    $icon = is_array($decodedIcon)
                        ? $decodedIcon
                        : [
                            'url' => $icon,
                            'public_id' => '',
                        ];
                }

                if (!is_array($icon)) {
                    $icon = [];
                }

                return [
                    'name' => trim(
                        (string) ($item['name'] ?? '')
                    ),
                    'icon' => [
                        'url' => $icon['url'] ?? null,
                        'public_id' => $icon['public_id'] ?? null,
                    ],
                    'url' => trim(
                        (string) ($item['url'] ?? '')
                    ),
                ];
            })
            ->filter(
                fn($item) =>
                $item['name'] !== '' ||
                    $item['url'] !== '' ||
                    !empty($item['icon']['url'])
            )
            ->values()
            ->toArray();

        return [
            'id' => !empty($detail['id'])
                ? (string) $detail['id']
                : null,
            'social_media' => $socialMedia,
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

                $item['title'] = trim(
                    (string) ($item['title'] ?? '')
                );

                $item['description'] = trim(
                    (string) ($item['description'] ?? '')
                );

                $item['categories'] = is_array($item['categories'] ?? null)
                    ? array_values($item['categories'])
                    : [];

                $item['active'] = $item['active'] ?? true;

                $item['date'] = $item['date'] ?? null;

                $item['author'] = $item['author'] ?? null;

                $item['seriesTags'] = is_array($item['seriesTags'] ?? null)
                    ? array_values($item['seriesTags'])
                    : [];

                $item['image'] = $item['image'] ?? null;

                $item['logo'] = $item['logo'] ?? null;

                $tableOfContents = $item['tableOfContents']
                    ?? $item['table_of_contents']
                    ?? [];

                if (is_string($tableOfContents)) {
                    $decoded = json_decode(
                        $tableOfContents,
                        true
                    );

                    $tableOfContents = is_array($decoded)
                        ? $decoded
                        : [];
                }

                if (!is_array($tableOfContents)) {
                    $tableOfContents = [];
                }

                $item['tableOfContents'] = collect($tableOfContents)
                    ->filter(fn($toc) => is_array($toc))
                    ->map(function ($toc, $index) {
                        $children = $toc['children'] ?? [];

                        if (!is_array($children)) {
                            $children = [];
                        }

                        return [
                            'id' => !empty($toc['id'])
                                ? (string) $toc['id']
                                : (string) Str::uuid(),

                            'order' => $toc['order'] ?? $index + 1,

                            'title' => trim(
                                (string) ($toc['title'] ?? '')
                            ),

                            'children' => collect($children)
                                ->map(
                                    fn($child) => trim(
                                        (string) $child
                                    )
                                )
                                ->filter()
                                ->values()
                                ->toArray(),
                        ];
                    })
                    ->filter(
                        fn($toc) => $toc['title'] !== ''
                    )
                    ->values()
                    ->toArray();

                $sections = $item['sections'] ?? [];

                if (is_string($sections)) {
                    $decoded = json_decode(
                        $sections,
                        true
                    );

                    $sections = is_array($decoded)
                        ? $decoded
                        : [];
                }

                if (!is_array($sections)) {
                    $sections = [];
                }

                $item['sections'] = collect($sections)
                    ->filter(fn($section) => is_array($section))
                    ->map(function ($section, $index) {
                        $section['id'] = !empty($section['id'])
                            ? (string) $section['id']
                            : (string) Str::uuid();

                        $section['type'] = $section['type'] ?? 'text';

                        $section['title'] = trim(
                            (string) (
                                $section['title']
                                ?? $section['heading']
                                ?? ''
                            )
                        );

                        $section['content'] = trim(
                            (string) (
                                $section['content']
                                ?? $section['description']
                                ?? ''
                            )
                        );

                        $section['image'] = $section['image'] ?? null;

                        return $section;
                    })
                    ->values()
                    ->toArray();

                return $item;
            })
            ->values()
            ->toArray();
    }
    public function updateItem(
        string $collection,
        array $input,
        array $images = []
    ): array {
        if (!in_array(
            $collection,
            [
                'banner',
                'categories',
                'caseStudies',
                'case-study-detail',
            ],
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

        if (!is_array($images)) {
            $images = [$images];
        }

        return match ($collection) {
            'banner' => $this->updateBanner(
                $data,
                $input,
                $images[0] ?? null
            ),

            'categories' => $this->updateCategory(
                $data,
                $input
            ),

            'caseStudies' => $this->updateCaseStudy(
                $data,
                $input,
                $images
            ),

            'case-study-detail' => $this->updateCaseStudyDetail(
                $data,
                $input,
                $images
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
        array $images = []
    ): array {
        $items = $data['caseStudies'] ?? [];

        if (!is_array($items)) {
            $items = [];
        }

        $id = $input['id'] ?? null;

        $existingIndex = null;
        $oldItem = null;

        foreach ($items as $index => $item) {
            if (
                is_array($item) &&
                (string) ($item['id'] ?? '') === (string) $id
            ) {
                $existingIndex = $index;
                $oldItem = $item;
                break;
            }
        }

        if (!is_array($oldItem)) {
            $oldItem = [];
        }

        if (!is_array($images)) {
            $images = [$images];
        }

        $image = $input['image'] ?? null;

        if (is_string($image)) {
            $decodedImage = json_decode($image, true);

            $image = is_array($decodedImage)
                ? $decodedImage
                : [];
        }

        if (!is_array($image)) {
            $image = [];
        }

        $imageIndex = $image['_image_index'] ?? null;

        if (
            is_numeric($imageIndex) &&
            isset($images[(int) $imageIndex]) &&
            $images[(int) $imageIndex] instanceof UploadedFile
        ) {
            $uploadedImage = $this->uploadService->uploadImage(
                $images[(int) $imageIndex],
                'case-study'
            );

            $input['image'] = [
                'url' => $uploadedImage['url'] ?? null,
                'public_id' => $uploadedImage['public_id'] ?? null,
            ];
        } elseif (!empty($oldItem['image'])) {
            $input['image'] = $oldItem['image'];
        } else {
            $input['image'] = null;
        }

        $logo = $input['logo'] ?? null;

        if (is_string($logo)) {
            $decodedLogo = json_decode($logo, true);

            $logo = is_array($decodedLogo)
                ? $decodedLogo
                : [];
        }

        if (!is_array($logo)) {
            $logo = [];
        }

        $logoIndex = $logo['_image_index'] ?? null;

        if (
            is_numeric($logoIndex) &&
            isset($images[(int) $logoIndex]) &&
            $images[(int) $logoIndex] instanceof UploadedFile
        ) {
            $uploadedLogo = $this->uploadService->uploadImage(
                $images[(int) $logoIndex],
                'case-study/logo'
            );

            $input['logo'] = [
                'url' => $uploadedLogo['url'] ?? null,
                'public_id' => $uploadedLogo['public_id'] ?? null,
            ];
        } elseif (!empty($oldItem['logo'])) {
            $input['logo'] = $oldItem['logo'];
        } else {
            $input['logo'] = null;
        }

        if (isset($input['sections'])) {
            $sections = $input['sections'];

            if (is_string($sections)) {
                $decodedSections = json_decode(
                    $sections,
                    true
                );

                $sections = is_array($decodedSections)
                    ? $decodedSections
                    : [];
            }

            if (!is_array($sections)) {
                $sections = [];
            }

            $oldSections = $oldItem['sections'] ?? [];

            if (is_string($oldSections)) {
                $decodedOldSections = json_decode(
                    $oldSections,
                    true
                );

                $oldSections = is_array($decodedOldSections)
                    ? $decodedOldSections
                    : [];
            }

            if (!is_array($oldSections)) {
                $oldSections = [];
            }

            $normalizedSections = [];

            foreach ($sections as $section) {
                if (!is_array($section)) {
                    continue;
                }

                $sectionId = $section['id'] ?? null;

                $oldSection = null;

                foreach ($oldSections as $oldSectionItem) {
                    if (
                        !is_array($oldSectionItem)
                    ) {
                        continue;
                    }

                    if (
                        $sectionId !== null &&
                        (string) ($oldSectionItem['id'] ?? '') ===
                        (string) $sectionId
                    ) {
                        $oldSection = $oldSectionItem;
                        break;
                    }
                }

                $sectionImage = $section['image'] ?? null;

                if (is_string($sectionImage)) {
                    $decodedSectionImage = json_decode(
                        $sectionImage,
                        true
                    );

                    $sectionImage = is_array(
                        $decodedSectionImage
                    )
                        ? $decodedSectionImage
                        : [];
                }

                if (!is_array($sectionImage)) {
                    $sectionImage = [];
                }

                $sectionImageIndex =
                    $sectionImage['_image_index'] ?? null;

                if (
                    is_numeric($sectionImageIndex) &&
                    isset(
                        $images[(int) $sectionImageIndex]
                    ) &&
                    $images[(int) $sectionImageIndex]
                    instanceof UploadedFile
                ) {
                    $uploadedSectionImage =
                        $this->uploadService->uploadImage(
                            $images[(int) $sectionImageIndex],
                            'case-study/content'
                        );

                    $section['image'] = [
                        'url' =>
                        $uploadedSectionImage['url'] ?? null,
                        'public_id' =>
                        $uploadedSectionImage['public_id'] ?? null,
                    ];
                } elseif (
                    !empty($section['removeImage'])
                ) {
                    $section['image'] = null;
                } elseif (
                    $oldSection !== null
                ) {
                    $section['image'] =
                        $oldSection['image'] ?? null;
                } else {
                    $section['image'] = null;
                }

                unset($section['removeImage']);

                if (
                    is_array($section['image'] ?? null)
                ) {
                    unset(
                        $section['image']['_image_index']
                    );
                }

                $normalizedSections[] = $section;
            }

            $input['sections'] = $normalizedSections;
        } elseif (isset($oldItem['sections'])) {
            $input['sections'] = $oldItem['sections'];
        }

        $input['id'] = $id
            ?: ($oldItem['id'] ?? (string) \Illuminate\Support\Str::uuid());

        $updatedItem = array_merge(
            $oldItem,
            $input
        );

        if ($existingIndex !== null) {
            $items[$existingIndex] = $updatedItem;
        } else {
            $items[] = $updatedItem;
        }

        $data['caseStudies'] = array_values($items);

        $this->homepageRepository->updateSection(
            'case_study_page',
            $data
        );

        return $updatedItem;
    }
    private function updateCaseStudyDetail(
        array $data,
        array $input,
        array $images = []
    ): array {
        $oldDetail = $data['caseStudyDetail'] ?? [];

        if (!is_array($oldDetail)) {
            $oldDetail = [];
        }

        $detailId = !empty($input['id'])
            ? (string) $input['id']
            : (
                !empty($oldDetail['id'])
                ? (string) $oldDetail['id']
                : (string) Str::uuid()
            );

        $socialMedia = $input['social_media'] ?? [];

        if (!is_array($socialMedia)) {
            $socialMedia = [];
        }

        if (!is_array($images)) {
            $images = [$images];
        }

        $oldSocialMedia = $oldDetail['social_media'] ?? [];

        if (!is_array($oldSocialMedia)) {
            $oldSocialMedia = [];
        }

        $normalizedSocialMedia = [];

        foreach ($socialMedia as $socialIndex => $social) {
            if (!is_array($social)) {
                continue;
            }

            $name = trim((string) ($social['name'] ?? ''));
            $url = trim((string) ($social['url'] ?? ''));

            $icon = $social['icon'] ?? [];

            if (is_string($icon)) {
                $decodedIcon = json_decode($icon, true);

                $icon = is_array($decodedIcon)
                    ? $decodedIcon
                    : [
                        'url' => $icon,
                        'public_id' => '',
                    ];
            }

            if (!is_array($icon)) {
                $icon = [];
            }

            $iconUrl = $icon['url'] ?? null;
            $iconPublicId = $icon['public_id'] ?? null;

            $oldSocial = $oldSocialMedia[$socialIndex] ?? [];

            if (!is_array($oldSocial)) {
                $oldSocial = [];
            }

            $oldIcon = $oldSocial['icon'] ?? [];

            if (is_string($oldIcon)) {
                $decodedOldIcon = json_decode($oldIcon, true);

                $oldIcon = is_array($decodedOldIcon)
                    ? $decodedOldIcon
                    : [
                        'url' => $oldIcon,
                        'public_id' => '',
                    ];
            }

            if (!is_array($oldIcon)) {
                $oldIcon = [];
            }

            if (empty($iconUrl)) {
                $iconUrl = $oldIcon['url'] ?? null;
            }

            if (empty($iconPublicId)) {
                $iconPublicId = $oldIcon['public_id'] ?? null;
            }

            $imageIndex = $social['_image_index'] ?? null;

            if ($imageIndex !== null && is_numeric($imageIndex)) {
                $imageIndex = (int) $imageIndex;
            } else {
                $imageIndex = null;
            }

            if (
                $imageIndex !== null &&
                isset($images[$imageIndex]) &&
                $images[$imageIndex] instanceof UploadedFile
            ) {
                $uploadedIcon = $this->uploadService->uploadImage(
                    $images[$imageIndex],
                    'case-study/social'
                );

                $newIconUrl = $uploadedIcon['url'] ?? null;
                $newIconPublicId = $uploadedIcon['public_id'] ?? null;

                if (
                    !empty($iconPublicId) &&
                    $iconPublicId !== $newIconPublicId
                ) {
                    $this->uploadService->deleteImage(
                        $iconPublicId
                    );
                }

                $iconUrl = $newIconUrl;
                $iconPublicId = $newIconPublicId;
            }

            if (
                $name === '' &&
                $url === '' &&
                empty($iconUrl)
            ) {
                continue;
            }

            $normalizedSocialMedia[] = [
                'name' => $name,
                'icon' => [
                    'url' => $iconUrl,
                    'public_id' => $iconPublicId,
                ],
                'url' => $url,
            ];
        }

        $updatedDetail = array_merge(
            $oldDetail,
            $input,
            [
                'id' => $detailId,
                'social_media' => $normalizedSocialMedia,
            ]
        );

        unset($updatedDetail['_image_index']);

        $data['caseStudyDetail'] = $updatedDetail;

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

        return $this->normalizeCaseStudyDetail(
            $savedData['caseStudyDetail'] ?? $updatedDetail
        );
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
                $this->uploadService->deleteImage(
                    $publicId
                );
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
