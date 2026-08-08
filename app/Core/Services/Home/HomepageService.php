<?php

namespace App\Core\Services\Home;

use App\Core\Repositories\Eloquent\HomepageRepository;
use App\Core\Services\Upload\UploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class HomepageService
{
    protected HomepageRepository $homepageRepository;
    protected UploadService $uploadService;
    public function __construct(
        HomepageRepository $homepageRepository,
        UploadService $uploadService
    ) {
        $this->homepageRepository = $homepageRepository;
        $this->uploadService = $uploadService;
    }
    public function getSection(
        string $section,
        array $defaults = []
    ): array {

        $data = array_merge(
            $defaults,
            $this->homepageRepository->getSection($section)
        );
        if (
            $section === 'success_stories' &&
            isset($data['successStories'])
        ) {
            $data['successStories'] = collect($data['successStories'])
                ->filter(fn($item) => is_array($item))
                ->map(function ($item) {
                    if (
                        isset($item['categories']) &&
                        is_string($item['categories'])
                    ) {

                        $item['categories'] = array_map(
                            'trim',
                            explode(',', $item['categories'])
                        );
                    }
                    if (empty($item['id'])) {

                        $item['id'] = (string) Str::uuid();
                    }

                    return $item;
                })
                ->values()
                ->toArray();
        }

        if (
            $section === 'news_sections' &&
            isset($data['sections'])
        ) {

            $changed = false;

            $data['sections'] = collect($data['sections'])
                ->filter(fn($item) => is_array($item))
                ->map(function ($item) use (&$changed) {
                    if (empty($item['id'])) {

                        $item['id'] = (string) Str::uuid();

                        $changed = true;
                    }
                    if (
                        isset($item['items']) &&
                        is_array($item['items'])
                    ) {

                        $item['items'] =
                            collect($item['items'])
                            ->filter(fn($child) => is_array($child))
                            ->map(function ($child) use (&$changed) {


                                if (empty($child['id'])) {

                                    $child['id'] =
                                        (string) Str::uuid();

                                    $changed = true;
                                }
                                return $child;
                            })
                            ->values()
                            ->toArray();
                    }
                    return $item;
                })
                ->values()
                ->toArray();
            if ($changed) {

                $this->homepageRepository
                    ->updateSection(
                        'news_sections',
                        [
                            'sections' =>
                            $data['sections']
                        ]
                    );
            }
        }
        if (
            $section === 'general_information' &&
            isset($data['generalInformations'])
        ) {
            $changed = false;

            $data['generalInformations'] =
                collect($data['generalInformations'])
                ->filter(fn($item) => is_array($item))
                ->map(function ($item) use (&$changed) {

                    if (empty($item['id'])) {

                        $item['id'] =
                            (string) Str::uuid();

                        $changed = true;
                    }



                    $item['badge'] =
                        $item['badge'] ?? '';

                    $item['title'] =
                        $item['title'] ?? '';

                    $item['description'] =
                        $item['description'] ?? '';



                    return $item;
                })
                ->values()
                ->toArray();



            if ($changed) {


                $this->homepageRepository
                    ->updateSection(
                        'general_information',
                        [
                            'generalInformations' =>
                            $data['generalInformations']
                        ]
                    );
            }
        }




        return $data;
    }

    public function getHomepage(): array
    {
        return [
            'banner' =>
            $this->getSection('banner'),
            'businessGrowth' =>
            $this->getSection(
                'business_growth',
                [
                    'packages' => []
                ]
            ),
            'introduction' =>
            $this->getSection('introduction'),
            'whyChooseCypress' =>
            $this->getSection(
                'why_choose_cypress',
                [
                    'benefits' => []
                ]
            ),
            'caseStudies' =>
            $this->getSection(
                'case_studies',
                [
                    'caseStudies' => []
                ]
            ),
            'pricing' =>
            $this->getSection('pricing'),
            'successStories' =>
            $this->getSection(
                'success_stories',
                [
                    'successStories' => []
                ]
            ),
            'generalInformation' =>
            $this->getSection(
                'general_information',
                [
                    'generalInformations' => []
                ]
            ),
            'news' =>
            $this->getSection(
                'news',
                [
                    'news' => []
                ]
            ),
            'sideNews' =>
            $this->getSection(
                'side_news',
                [
                    'sideNews' => []
                ]
            ),
            'newsSections' =>
            $this->getSection(
                'news_sections',
                [
                    'sections' => []
                ]
            ),
            'contact' =>
            $this->getSection(
                'contact',
                [
                    'labels' => []
                ]
            ),
            'launchOffer' =>
            $this->getSection(
                'launch_offer',
                [
                    'title' => '',
                    'subtitle' => '',
                    'buttonText' => '',
                    'buttonUrl' => '',
                    'expiryDate' => '',
                    'seats' => '',
                ]
            ),
        ];
    }

    public function uploadImage(
        UploadedFile $file,
        string $folder
    ): array {
        $uploaded =
            $this->uploadService->uploadImage(
                $file,
                $folder
            );
        return [
            'url' =>
            $uploaded['url'],
            'public_id' =>
            $uploaded['public_id'],
        ];
    }

    public function updateHomepage(
        string $section,
        array $input,
        ?UploadedFile $image = null
    ): array {
        return $this->updateSection(
            $section,
            $input,
            $image
        );
    }


    public function updateSection(
        string $section,
        array $data,
        ?UploadedFile $image = null
    ): array {

        /**
         * Frontend section key -> database section key
         */
        $sectionMap = [
            'businessGrowth' => 'business_growth',
            'whyChooseCypress' => 'why_choose_cypress',
            'successStories' => 'success_stories',
            'caseStudies' => 'case_studies',
            'generalInformation' => 'general_information',
            'newsSections' => 'news_sections',
            'sideNews' => 'side_news',
        ];

        if (isset($sectionMap[$section])) {
            $section = $sectionMap[$section];
        }

        /**
         * Single image sections
         */
        $singleImageSections = [
            'banner',
            'contact',
            'introduction',
        ];

        if (
            $image &&
            in_array($section, $singleImageSections, true)
        ) {
            $data['image'] = $this->uploadImage(
                $image,
                "homepage/$section"
            );
        }

        /**
         * =========================================================
         * BUSINESS GROWTH
         * =========================================================
         */
        if (
            $section === 'business_growth' &&
            isset($data['packages']) &&
            is_array($data['packages'])
        ) {

            foreach ($data['packages'] as &$package) {

                if (empty($package['id'])) {
                    $package['id'] = (string) Str::uuid();
                }

                $package['active'] =
                    $package['active'] ?? true;

                $package['number'] =
                    (string) ($package['number'] ?? '');

                $package['title'] =
                    $package['title'] ?? '';

                $package['packageName'] =
                    $package['packageName'] ?? '';

                $package['headline'] =
                    $package['headline'] ?? '';

                $package['description'] =
                    $package['description'] ?? '';

                $package['color'] =
                    $package['color'] ?? '#2563eb';
            }

            unset($package);
        }

        /**
         * =========================================================
         * MAIN NEWS
         *
         * Quan trọng:
         *
         * 1. ID tồn tại -> UPDATE item đó
         * 2. ID mới -> APPEND item mới
         * 3. Không được ghi đè item cũ bằng item mới
         * =========================================================
         */
        if (
            $section === 'news' &&
            isset($data['news']) &&
            is_array($data['news'])
        ) {

            /**
             * Lấy dữ liệu hiện tại trong DB
             */
            $oldData = $this->homepageRepository
                ->getSection('news');

            $oldNews = $oldData['news'] ?? [];

            if (!is_array($oldNews)) {
                $oldNews = [];
            }

            /**
             * Nếu frontend gửi toàn bộ list
             * thì xử lý từng item.
             */
            foreach ($data['news'] as &$incomingItem) {

                /**
                 * Đảm bảo item là array
                 */
                if (!is_array($incomingItem)) {
                    continue;
                }

                /**
                 * =================================================
                 * CREATE
                 * =================================================
                 *
                 * Nếu không có ID -> tạo UUID mới.
                 */
                if (empty($incomingItem['id'])) {

                    $incomingItem['id'] =
                        (string) Str::uuid();
                }

                $incomingId =
                    (string) $incomingItem['id'];

                /**
                 * =================================================
                 * FIND OLD ITEM
                 * =================================================
                 */
                $oldIndex = collect($oldNews)
                    ->search(function ($oldItem) use ($incomingId) {

                        return is_array($oldItem)
                            &&
                            isset($oldItem['id'])
                            &&
                            (string) $oldItem['id'] === $incomingId;
                    });

                /**
                 * =================================================
                 * UPLOAD IMAGE
                 * =================================================
                 */
                if ($image) {

                    $incomingItem['image'] =
                        $this->uploadImage(
                            $image,
                            'homepage/news'
                        );
                }

                /**
                 * =================================================
                 * UPDATE EXISTING ITEM
                 * =================================================
                 */
                if (
                    $oldIndex !== false &&
                    isset($oldNews[$oldIndex])
                ) {

                    /**
                     * Nếu không upload ảnh mới
                     * thì giữ ảnh cũ.
                     */
                    if (
                        !$image &&
                        isset($oldNews[$oldIndex]['image'])
                    ) {
                        $incomingItem['image'] =
                            $oldNews[$oldIndex]['image'];
                    }

                    /**
                     * Merge:
                     *
                     * old:
                     * {
                     *   id: 1,
                     *   title: "Old",
                     *   category: "A"
                     * }
                     *
                     * new:
                     * {
                     *   id: 1,
                     *   title: "New"
                     * }
                     *
                     * result:
                     * {
                     *   id: 1,
                     *   title: "New",
                     *   category: "A"
                     * }
                     */
                    $oldNews[$oldIndex] =
                        array_merge(
                            $oldNews[$oldIndex],
                            $incomingItem
                        );
                } else {

                    /**
                     * =================================================
                     * CREATE NEW ITEM
                     * =================================================
                     *
                     * ID không tồn tại trong DB
                     * => APPEND, KHÔNG UPDATE item cũ.
                     */
                    $oldNews[] = $incomingItem;
                }
            }

            unset($incomingItem);

            /**
             * Save toàn bộ danh sách sau khi
             * đã update/create đúng item.
             */
            $data['news'] =
                array_values($oldNews);
        }

        /**
         * =========================================================
         * SUCCESS STORIES
         * =========================================================
         */
        if (
            $section === 'success_stories' &&
            isset($data['successStories']) &&
            is_array($data['successStories'])
        ) {

            $oldData = $this->homepageRepository
                ->getSection('success_stories');

            $oldStories =
                $oldData['successStories'] ?? [];

            if (!is_array($oldStories)) {
                $oldStories = [];
            }

            foreach ($data['successStories'] as &$incomingItem) {

                if (!is_array($incomingItem)) {
                    continue;
                }

                if (empty($incomingItem['id'])) {
                    $incomingItem['id'] =
                        (string) Str::uuid();

                    $oldStories[] =
                        $incomingItem;

                    continue;
                }

                $incomingId =
                    (string) $incomingItem['id'];

                $oldIndex = collect($oldStories)
                    ->search(function ($oldItem) use ($incomingId) {

                        return is_array($oldItem)
                            &&
                            isset($oldItem['id'])
                            &&
                            (string) $oldItem['id'] === $incomingId;
                    });

                if ($oldIndex !== false) {

                    if ($image) {

                        $incomingItem['avatar'] =
                            $this->uploadImage(
                                $image,
                                'homepage/success-stories'
                            );
                    } elseif (
                        isset($oldStories[$oldIndex]['avatar'])
                    ) {

                        $incomingItem['avatar'] =
                            $oldStories[$oldIndex]['avatar'];
                    }

                    $oldStories[$oldIndex] =
                        array_merge(
                            $oldStories[$oldIndex],
                            $incomingItem
                        );
                } else {

                    if ($image) {

                        $incomingItem['avatar'] =
                            $this->uploadImage(
                                $image,
                                'homepage/success-stories'
                            );
                    }

                    $oldStories[] =
                        $incomingItem;
                }
            }

            unset($incomingItem);

            $data['successStories'] =
                array_values($oldStories);
        }

        /**
         * =========================================================
         * SIDE NEWS
         * =========================================================
         */
        if (
            $section === 'side_news' &&
            (
                isset($data['items']) ||
                isset($data['sideNews'])
            )
        ) {

            $oldData = $this->homepageRepository
                ->getSection('side_news');

            $targetKey = isset($data['sideNews'])
                ? 'sideNews'
                : 'items';

            $oldItems = $oldData['sideNews'] ?? ($oldData['items'] ?? []);

            if (!is_array($oldItems)) {
                $oldItems = [];
            }

            foreach ($data[$targetKey] as &$incomingItem) {
                if (!is_array($incomingItem)) {
                    continue;
                }

                if (empty($incomingItem['id'])) {
                    $incomingItem['id'] =
                        (string) Str::uuid();
                }

                $incomingId = (string) $incomingItem['id'];

                $oldIndex = collect($oldItems)
                    ->search(function ($oldItem) use ($incomingId) {
                        return is_array($oldItem)
                            && isset($oldItem['id'])
                            && (string) $oldItem['id'] === $incomingId;
                    });

                if ($image) {
                    $incomingItem['image'] = $this->uploadImage(
                        $image,
                        'homepage/side-news'
                    );
                } elseif (
                    $oldIndex !== false &&
                    isset($oldItems[$oldIndex]['image'])
                ) {
                    $incomingItem['image'] = $oldItems[$oldIndex]['image'];
                }

                if ($oldIndex !== false) {
                    $oldItems[$oldIndex] = array_merge(
                        $oldItems[$oldIndex],
                        $incomingItem
                    );
                } else {
                    $oldItems[] = $incomingItem;
                }
            }

            unset($incomingItem);

            $data['sideNews'] = array_values($oldItems);
            unset($data['items']);
        }

        /**
         * =========================================================
         * NEWS SECTIONS
         * =========================================================
         */
        if (
            $section === 'news_sections' &&
            isset($data['sections']) &&
            is_array($data['sections'])
        ) {

            $oldData = $this->homepageRepository
                ->getSection('news_sections');

            $oldSections =
                $oldData['sections'] ?? [];

            if (!is_array($oldSections)) {
                $oldSections = [];
            }

            foreach ($data['sections'] as &$sectionItem) {

                if (!is_array($sectionItem)) {
                    continue;
                }

                if (empty($sectionItem['id'])) {
                    $sectionItem['id'] =
                        (string) Str::uuid();
                }

                if (
                    isset($sectionItem['items']) &&
                    is_array($sectionItem['items'])
                ) {

                    foreach (
                        $sectionItem['items']
                        as &$item
                    ) {

                        if (!is_array($item)) {
                            continue;
                        }

                        if (empty($item['id'])) {
                            $item['id'] =
                                (string) Str::uuid();
                        }
                    }

                    unset($item);
                }
            }

            unset($sectionItem);

            $data['sections'] =
                array_values($data['sections']);
        }

        /**
         * =========================================================
         * SAVE
         * =========================================================
         */
        return $this->homepageRepository
            ->updateSection(
                $section,
                $data
            );
    }



    // public function updateItem(
    //     string $section,
    //     string $field,
    //     array $input,
    //     ?array $uploadFields = null,
    //     ?string $folder = null
    // ): array {

    //     $sectionMap = [
    //         'generalInformation' => 'general_information',
    //         'successStories' => 'success_stories',
    //         'caseStudies' => 'case_studies',
    //         'businessGrowth' => 'business_growth',
    //         'whyChooseCypress' => 'why_choose_cypress',
    //         'sideNews' => 'side_news',
    //         'newsSections' => 'news_sections',
    //         'news' => 'news',
    //         'contact' => 'contact',
    //         'launchOffer' => 'launch_offer',
    //         'banner' => 'banner',
    //         'introduction' => 'introduction',
    //         'pricing' => 'pricing',
    //     ];

    //     if (isset($sectionMap[$section])) {
    //         $section = $sectionMap[$section];
    //     }

    //     $data = $this->getSection(
    //         $section,
    //         [
    //             $field => []
    //         ]
    //     );


    //     $items = $data[$field] ?? [];



    //     /**
    //      * Upload multiple files
    //      */
    //     if ($uploadFields) {

    //         foreach ($uploadFields as $uploadField) {

    //             if (
    //                 isset($input[$uploadField]) &&
    //                 $input[$uploadField] instanceof UploadedFile
    //             ) {

    //                 $input[$uploadField] =
    //                     $this->uploadImage(
    //                         $input[$uploadField],
    //                         $folder ?? "homepage/$section"
    //                     );
    //             }
    //         }
    //     }



    //     /**
    //      * CREATE
    //      */
    //     if (empty($input['id'])) {


    //         $input['id'] =
    //             (string) Str::uuid();
    //         $result['id'] = $input['id']; // Update result ID for new item


    //         $items[] = $input;
    //     } else {


    //         /**
    //          * UPDATE
    //          */
    //         foreach ($items as $index => $item) {


    //             if (
    //                 isset($item['id']) &&
    //                 (string)$item['id'] === (string)$input['id']
    //             ) {


    //                 $items[$index] =
    //                     array_merge(
    //                         $item,
    //                         $input
    //                     );


    //                 break;
    //             }
    //         }
    //     }



    //     $data[$field] = $items;



    //     $this->homepageRepository
    //         ->updateSection(
    //             $section,
    //             $data
    //         );


    //     return $input;
    // }

    public function updateItem(
        string $section,
        string $field,
        array $input,
        ?array $uploadFields = null,
        ?string $folder = null,
        ?UploadedFile $image = null,
        ?UploadedFile $logo = null
    ): array {


        /**
         * Frontend section key -> Database section key
         */
        $sectionMap = [
            'generalInformation' => 'general_information',
            'successStories' => 'success_stories',
            'caseStudies' => 'case_studies',
            'businessGrowth' => 'business_growth',
            'whyChooseCypress' => 'why_choose_cypress',
            'sideNews' => 'side_news',
            'newsSections' => 'news_sections',
            'news' => 'news',
            'contact' => 'contact',
            'launchOffer' => 'launch_offer',
            'banner' => 'banner',
            'introduction' => 'introduction',
            'pricing' => 'pricing',
        ];


        if (isset($sectionMap[$section])) {
            $section = $sectionMap[$section];
        }



        /**
         * Get old section data
         */
        $data = $this->getSection(
            $section,
            [
                $field => []
            ]
        );


        $items = $data[$field] ?? [];


        if (!is_array($items)) {
            $items = [];
        }



        /**
         * Upload multiple fields
         *
         * Example:
         * [
         *   'icon' => UploadedFile,
         *   'avatar' => UploadedFile
         * ]
         */
        if ($uploadFields) {

            foreach ($uploadFields as $key => $file) {

                if ($file instanceof UploadedFile) {

                    $input[$key] =
                        $this->uploadImage(
                            $file,
                            $folder ?? "homepage/$section"
                        );
                }
            }
        }




        /**
         * Upload image
         */
        if ($image instanceof UploadedFile) {

            $input['image'] =
                $this->uploadImage(
                    $image,
                    $folder ?? "homepage/$section"
                );
        }




        /**
         * Upload logo
         */
        if ($logo instanceof UploadedFile) {

            $input['logo'] =
                $this->uploadImage(
                    $logo,
                    $folder ?? "homepage/$section"
                );
        }




        /**
         * CREATE ITEM
         */
        if (empty($input['id'])) {


            $input['id'] =
                (string) Str::uuid();


            $items[] = $input;
        }




        /**
         * UPDATE ITEM
         */
        else {


            $updated = false;


            foreach ($items as $index => $item) {



                if (
                    isset($item['id']) &&
                    (string)$item['id'] === (string)$input['id']
                ) {



                    /**
                     * Keep old images
                     */
                    foreach (
                        [
                            'image',
                            'logo',
                            'avatar',
                            'icon'
                        ] as $fileField
                    ) {



                        if (
                            !isset($input[$fileField]) &&
                            isset($item[$fileField])
                        ) {


                            $input[$fileField] =
                                $item[$fileField];
                        }
                    }




                    /**
                     * Merge old data + new data
                     */
                    $items[$index] =
                        array_merge(
                            $item,
                            $input
                        );


                    $updated = true;


                    break;
                }
            }




            /**
             * ID không tồn tại
             * => create new item
             */
            if (!$updated) {


                $items[] = $input;
            }
        }




        /**
         * Save section
         */
        $data[$field] =
            array_values($items);



        $this->homepageRepository
            ->updateSection(
                $section,
                $data
            );



        return $input;
    }
    public function deleteItem(
        string $section,
        string $field,
        string $id
    ): bool {

        /**
         * Map frontend section key -> database section key
         */
        $sectionMap = [
            'generalInformation' => 'general_information',
            'successStories' => 'success_stories',
            'caseStudies' => 'case_studies',
            'businessGrowth' => 'business_growth',
            'whyChooseCypress' => 'why_choose_cypress',
            'sideNews' => 'side_news',
            'newsSections' => 'news_sections',
        ];


        if (isset($sectionMap[$section])) {
            $section = $sectionMap[$section];
        }


        $data = $this->getSection($section);


        $items = collect(
            $data[$field] ?? []
        );


        /**
         * Find item by ID
         */
        $deleteItem = $items->first(
            fn($item) =>
            isset($item['id'])
                &&
                (string)$item['id'] === (string)$id
        );


        /**
         * Item not found
         */
        if (!$deleteItem) {

            logger()->warning(
                'Homepage delete item not found',
                [
                    'section' => $section,
                    'field' => $field,
                    'id' => $id,
                    'items' => $items->pluck('id')->toArray()
                ]
            );

            return false;
        }


        /**
         * Delete image if exists
         */
        if (
            isset($deleteItem['image']['public_id'])
            &&
            $deleteItem['image']['public_id']
        ) {

            $this->uploadService->deleteImage(
                $deleteItem['image']['public_id']
            );
        }


        if (
            isset($deleteItem['avatar']['public_id'])
            &&
            $deleteItem['avatar']['public_id']
        ) {

            $this->uploadService->deleteImage(
                $deleteItem['avatar']['public_id']
            );
        }


        /**
         * Remove item
         */
        $data[$field] =
            $items
            ->reject(
                fn($item) =>
                isset($item['id'])
                    &&
                    (string)$item['id'] === (string)$id
            )
            ->values()
            ->toArray();



        /**
         * Save
         */
        $this->homepageRepository
            ->updateSection(
                $section,
                $data
            );


        return true;
    }
}
