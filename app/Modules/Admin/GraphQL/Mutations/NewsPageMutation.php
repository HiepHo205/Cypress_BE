<?php

namespace App\Modules\Admin\GraphQL\Mutations;

use App\Core\Services\News\NewsPageService;
use Illuminate\Validation\ValidationException;

class NewsPageMutation
{
    public function __construct(
        private NewsPageService $newsPageService
    ) {}

    public function update($_, array $args): array
    {
        $collection = $args['collection'] ?? null;

        $allowedCollections = [
            'latest',
            'featured',
            'categories',
            'banner',
            'newsletter',
        ];

        if (
            !$collection ||
            !in_array($collection, $allowedCollections, true)
        ) {
            throw ValidationException::withMessages([
                'collection' => [
                    sprintf(
                        'Invalid News page collection "%s". Allowed values: %s.',
                        $collection ?? 'null',
                        implode(', ', $allowedCollections)
                    ),
                ],
            ]);
        }

        $input = json_decode(
            json_encode($args['input'] ?? []),
            true
        );

        /*
         * Banner / Newsletter / Categories
         *
         * Banner cần nhận image để upload Cloudinary.
         */
        if (
            in_array(
                $collection,
                ['categories', 'banner', 'newsletter'],
                true
            )
        ) {
            return $this->newsPageService->updateSection(
                $collection,
                $input,
                $args['image'] ?? null
            );
        }

        /*
         * Latest / Featured
         */
        return $this->newsPageService->updateItem(
            $collection,
            $input,
            $args['image'] ?? null
        );
    }

    public function deleteItem($_, array $args): bool
    {
        $collection = $args['collection'] ?? null;

        $allowedCollections = [
            'latest',
            'featured',
            'categories',
        ];

        if (
            !$collection ||
            !in_array($collection, $allowedCollections, true)
        ) {
            throw ValidationException::withMessages([
                'collection' => [
                    sprintf(
                        'Invalid News page collection "%s". Allowed values: %s.',
                        $collection ?? 'null',
                        implode(', ', $allowedCollections)
                    ),
                ],
            ]);
        }

        return $this->newsPageService->deleteItem(
            $collection,
            (string) $args['id']
        );
    }
}
