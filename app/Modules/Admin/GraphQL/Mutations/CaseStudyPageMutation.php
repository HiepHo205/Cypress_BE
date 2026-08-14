<?php

namespace App\Modules\Admin\GraphQL\Mutations;

use App\Core\Services\CaseStudy\CaseStudyService;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class CaseStudyPageMutation
{
    public function __construct(
        private CaseStudyService $caseStudyService
    ) {}

    public function updateCaseStudy($_, array $args): array
    {
        $section = $args['section'] ?? null;

        if (!in_array($section, [
            'banner',
            'categories',
            'caseStudies',
        ], true)) {
            throw ValidationException::withMessages([
                'section' => [
                    'Invalid Case Study section. Allowed values: banner, categories, caseStudies.',
                ],
            ]);
        }

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

        if (
            $section === 'categories' &&
            isset($input['children'])
        ) {
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
                                (string) (
                                    $child['name'] ?? ''
                                )
                            );

                            if ($name === '') {
                                return null;
                            }

                            $result = [
                                'name' => $name,
                            ];

                            if (
                                !empty($child['id'])
                            ) {
                                $result['id'] =
                                    (string) $child['id'];
                            }

                            return $result;
                        },
                        $input['children']
                    )
                )
            );
        }

        Log::info(
            'CASE STUDY MUTATION INPUT',
            [
                'section' => $section,
                'input' => $input,
                'children' =>
                $section === 'categories'
                    ? ($input['children'] ?? [])
                    : null,
                'children_count' =>
                $section === 'categories'
                    ? count(
                        $input['children'] ?? []
                    )
                    : null,
            ]
        );

        $result =
            $this->caseStudyService->updateItem(
                $section,
                $input,
                $args['image'] ?? null
            );

        Log::info(
            'CASE STUDY MUTATION RESULT',
            [
                'section' => $section,
                'result' => $result,
                'children' =>
                $section === 'categories'
                    ? ($result['children'] ?? [])
                    : null,
                'children_count' =>
                $section === 'categories'
                    ? count(
                        $result['children'] ?? []
                    )
                    : null,
            ]
        );

        return $result;
    }

    public function deleteCaseStudy(
        $_,
        array $args
    ): bool {
        $section = $args['section'] ?? null;

        if (!in_array($section, [
            'categories',
            'caseStudies',
        ], true)) {
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
