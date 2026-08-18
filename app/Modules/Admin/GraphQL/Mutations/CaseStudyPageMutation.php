<?php

namespace App\Modules\Admin\GraphQL\Mutations;

use App\Core\Services\CaseStudy\CaseStudyService;
use Illuminate\Validation\ValidationException;

class CaseStudyPageMutation
{
    public function __construct(
        private CaseStudyService $caseStudyService
    ) {}

    public function updateCaseStudy($_, array $args): array
    {
        $section = $args['section'] ?? null;

        if (!in_array(
            $section,
            [
                'banner',
                'categories',
                'caseStudies',
                'case-study-detail',
            ],
            true
        )) {
            throw ValidationException::withMessages([
                'section' => [
                    'Invalid Case Study section. Allowed values: banner, categories, caseStudies, case-study-detail.',
                ],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Normalize input
        |--------------------------------------------------------------------------
        */
        $input = $args['input'] ?? null;

        if (is_string($input)) {
            $decoded = json_decode(
                $input,
                true
            );

            if (
                json_last_error() !== JSON_ERROR_NONE ||
                !is_array($decoded)
            ) {
                throw ValidationException::withMessages([
                    'input' => [
                        'Invalid Case Study input JSON.',
                    ],
                ]);
            }

            $input = $decoded;
        }

        if (is_object($input)) {
            $input = json_decode(
                json_encode($input),
                true
            );
        }

        if (!is_array($input)) {
            throw ValidationException::withMessages([
                'input' => [
                    'Case Study input must be an object.',
                ],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | ID from argument has priority
        |--------------------------------------------------------------------------
        */
        if (!empty($args['id'])) {
            $input['id'] = (string) $args['id'];
        }

        if (empty($input)) {
            throw ValidationException::withMessages([
                'input' => [
                    'Case Study input is required.',
                ],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Categories
        |--------------------------------------------------------------------------
        */
        if ($section === 'categories') {
            $input = $this->normalizeCategoryInput(
                $input
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Case Study Detail
        |--------------------------------------------------------------------------
        */
        if ($section === 'case-study-detail') {
            $input = $this->normalizeCaseStudyDetailInput(
                $input
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Images
        |--------------------------------------------------------------------------
        */
        $images = $args['images'] ?? [];

        if (!is_array($images)) {
            $images = [$images];
        }

        /*
        |--------------------------------------------------------------------------
        | Update service
        |--------------------------------------------------------------------------
        */
        return $this->caseStudyService->updateItem(
            $section,
            $input,
            $images
        );
    }

    /**
     * Normalize category input.
     */
    private function normalizeCategoryInput(
        array $input
    ): array {
        if (!isset($input['children'])) {
            return $input;
        }

        if (!is_array($input['children'])) {
            throw ValidationException::withMessages([
                'input.children' => [
                    'Category children must be an array.',
                ],
            ]);
        }

        $input['children'] = array_values(
            array_filter(
                array_map(
                    function ($child) {
                        if (!is_array($child)) {
                            return null;
                        }

                        $name = trim(
                            (string) ($child['name'] ?? '')
                        );

                        if ($name === '') {
                            return null;
                        }

                        $result = [
                            'name' => $name,
                        ];

                        if (!empty($child['id'])) {
                            $result['id'] =
                                (string) $child['id'];
                        }

                        return $result;
                    },
                    $input['children']
                )
            )
        );

        return $input;
    }

    /**
     * Normalize Case Study Detail input.
     */
    private function normalizeCaseStudyDetailInput(
        array $input
    ): array {
        /*
        |--------------------------------------------------------------------------
        | Social Media
        |--------------------------------------------------------------------------
        */
        if (
            isset($input['social_media']) &&
            !is_array($input['social_media'])
        ) {
            throw ValidationException::withMessages([
                'input.social_media' => [
                    'Social media must be an array.',
                ],
            ]);
        }

        if (isset($input['social_media'])) {
            $input['social_media'] =
                $this->normalizeSocialMedia(
                    $input['social_media']
                );
        } else {
            /*
             * Do not force social_media to exist.
             *
             * This allows the detail API to update
             * other fields without accidentally deleting
             * existing social media.
             */
            $input['social_media'] = [];
        }

        /*
        |--------------------------------------------------------------------------
        | Table Of Contents
        |--------------------------------------------------------------------------
        */
        if (
            isset($input['tableOfContents']) &&
            !is_array($input['tableOfContents'])
        ) {
            throw ValidationException::withMessages([
                'input.tableOfContents' => [
                    'Table of contents must be an array.',
                ],
            ]);
        }

        if (
            isset($input['table_of_contents']) &&
            !is_array($input['table_of_contents'])
        ) {
            throw ValidationException::withMessages([
                'input.table_of_contents' => [
                    'Table of contents must be an array.',
                ],
            ]);
        }

        if (isset($input['tableOfContents'])) {
            $input['tableOfContents'] =
                $this->normalizeTableOfContents(
                    $input['tableOfContents']
                );
        }

        if (isset($input['table_of_contents'])) {
            $input['tableOfContents'] =
                $this->normalizeTableOfContents(
                    $input['table_of_contents']
                );

            unset($input['table_of_contents']);
        }

        /*
        |--------------------------------------------------------------------------
        | Sections
        |--------------------------------------------------------------------------
        */
        if (
            isset($input['sections']) &&
            !is_array($input['sections'])
        ) {
            throw ValidationException::withMessages([
                'input.sections' => [
                    'Case Study sections must be an array.',
                ],
            ]);
        }

        if (isset($input['sections'])) {
            $input['sections'] =
                $this->normalizeSections(
                    $input['sections']
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Basic fields
        |--------------------------------------------------------------------------
        */
        if (isset($input['date'])) {
            $input['date'] = trim(
                (string) $input['date']
            );
        }

        if (isset($input['clientName'])) {
            $input['clientName'] = trim(
                (string) $input['clientName']
            );
        }

        if (isset($input['author'])) {
            $input['author'] = trim(
                (string) $input['author']
            );
        }

        if (isset($input['planTitle'])) {
            $input['planTitle'] = trim(
                (string) $input['planTitle']
            );
        }

        if (isset($input['description'])) {
            $input['description'] = trim(
                (string) $input['description']
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Series Tags
        |--------------------------------------------------------------------------
        */
        if (isset($input['seriesTags'])) {
            if (!is_array($input['seriesTags'])) {
                throw ValidationException::withMessages([
                    'input.seriesTags' => [
                        'Series tags must be an array.',
                    ],
                ]);
            }

            $input['seriesTags'] = array_values(
                array_filter(
                    array_map(
                        fn($item) =>
                        trim((string) $item),
                        $input['seriesTags']
                    )
                )
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Categories
        |--------------------------------------------------------------------------
        */
        if (isset($input['categories'])) {
            if (!is_array($input['categories'])) {
                throw ValidationException::withMessages([
                    'input.categories' => [
                        'Case Study categories must be an array.',
                    ],
                ]);
            }

            $input['categories'] = array_values(
                array_filter(
                    array_map(
                        fn($item) =>
                        trim((string) $item),
                        $input['categories']
                    )
                )
            );

            if (count($input['categories']) > 2) {
                throw ValidationException::withMessages([
                    'input.categories' => [
                        'A Case Study can have a maximum of 2 categories.',
                    ],
                ]);
            }
        }

        return $input;
    }

    private function normalizeSocialMedia(
        array $socialMedia
    ): array {
        return array_values(
            array_filter(
                array_map(
                    function ($social) {
                        if (!is_array($social)) {
                            return null;
                        }

                        $name = trim(
                            (string) ($social['name'] ?? '')
                        );

                        $url = trim(
                            (string) ($social['url'] ?? '')
                        );

                        $icon = $social['icon'] ?? [];

                        /*
                        |--------------------------------------------------------------------------
                        | Icon can be JSON string
                        |--------------------------------------------------------------------------
                        */
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

                        $result = [
                            'name' => $name,

                            'icon' => [
                                'url' => !empty($icon['url'])
                                    ? (string) $icon['url']
                                    : null,

                                'public_id' =>
                                !empty($icon['public_id'])
                                    ? (string) $icon['public_id']
                                    : null,
                            ],

                            'url' => $url,
                        ];

                        /*
                        |--------------------------------------------------------------------------
                        | Keep existing ID
                        |--------------------------------------------------------------------------
                        */
                        if (!empty($social['id'])) {
                            $result['id'] =
                                (string) $social['id'];
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | New uploaded icon index
                        |--------------------------------------------------------------------------
                        */
                        if (
                            isset($social['_image_index']) &&
                            is_numeric(
                                $social['_image_index']
                            )
                        ) {
                            $result['_image_index'] =
                                (int) $social['_image_index'];
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | Remove icon
                        |--------------------------------------------------------------------------
                        */
                        if (
                            !empty($social['removeIcon'])
                        ) {
                            $result['removeIcon'] = true;
                        }

                        return $result;
                    },
                    $socialMedia
                )
            )
        );
    }

    private function normalizeTableOfContents(
        array $items
    ): array {
        return array_values(
            array_filter(
                array_map(
                    function ($item, $index) {
                        if (!is_array($item)) {
                            return null;
                        }

                        $title = trim(
                            (string) ($item['title'] ?? '')
                        );

                        $children =
                            $item['children'] ?? [];

                        if (!is_array($children)) {
                            $children = [];
                        }

                        $children = array_values(
                            array_filter(
                                array_map(
                                    fn($child) =>
                                    trim((string) $child),
                                    $children
                                )
                            )
                        );

                        /*
                        |--------------------------------------------------------------------------
                        | Ignore completely empty TOC item
                        |--------------------------------------------------------------------------
                        */
                        if (
                            $title === '' &&
                            empty($children)
                        ) {
                            return null;
                        }

                        $result = [
                            'title' => $title,

                            'children' => $children,

                            'order' => $index + 1,
                        ];

                        if (!empty($item['id'])) {
                            $result['id'] =
                                (string) $item['id'];
                        }

                        return $result;
                    },
                    $items,
                    array_keys($items)
                )
            )
        );
    }

    private function normalizeSections(
        array $sections
    ): array {
        return array_values(
            array_filter(
                array_map(
                    function ($section, $index) {
                        if (!is_array($section)) {
                            return null;
                        }

                        $title = trim(
                            (string) ($section['title'] ?? '')
                        );

                        $content = trim(
                            (string) ($section['content'] ?? '')
                        );

                        $image = $section['image'] ?? null;

                        if (is_string($image)) {
                            $decodedImage = json_decode(
                                $image,
                                true
                            );

                            $image = is_array($decodedImage)
                                ? $decodedImage
                                : [
                                    'url' => $image,
                                    'public_id' => '',
                                ];
                        }

                        if (!is_array($image)) {
                            $image = [];
                        }

                        $normalizedImage = [
                            'url' => !empty($image['url'])
                                ? (string) $image['url']
                                : null,

                            'public_id' =>
                            !empty($image['public_id'])
                                ? (string) $image['public_id']
                                : null,
                        ];

                        if (
                            isset($image['_image_index']) &&
                            is_numeric($image['_image_index'])
                        ) {
                            $normalizedImage['_image_index'] =
                                (int) $image['_image_index'];
                        }

                        $result = [
                            'title' => $title,
                            'content' => $content,
                            'image' => $normalizedImage,
                            'order' => $index + 1,
                        ];

                        if (!empty($section['id'])) {
                            $result['id'] =
                                (string) $section['id'];
                        }

                        if (!empty($section['removeImage'])) {
                            $result['removeImage'] = true;
                        }

                        $hasImageIndex =
                            isset($image['_image_index']) &&
                            is_numeric($image['_image_index']);

                        if (
                            $title === '' &&
                            $content === '' &&
                            empty($image['url']) &&
                            !$hasImageIndex
                        ) {
                            return null;
                        }

                        return $result;
                    },
                    $sections,
                    array_keys($sections)
                )
            )
        );
    }

    public function deleteCaseStudy(
        $_,
        array $args
    ): bool {
        $section = $args['section'] ?? null;

        if (!in_array(
            $section,
            [
                'categories',
                'caseStudies',
            ],
            true
        )) {
            throw ValidationException::withMessages([
                'section' => [
                    'Invalid Case Study section. Allowed values: categories, caseStudies.',
                ],
            ]);
        }

        if (empty($args['id'])) {
            throw ValidationException::withMessages([
                'id' => [
                    'Case Study ID is required.',
                ],
            ]);
        }

        return $this->caseStudyService->deleteItem(
            $section,
            (string) $args['id']
        );
    }
}
