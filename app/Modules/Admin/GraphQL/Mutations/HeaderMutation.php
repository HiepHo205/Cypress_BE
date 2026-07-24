<?php

namespace App\Modules\Admin\GraphQL\Mutations;

use App\Core\Services\Layout\Header\HeaderService;

class HeaderMutation
{
    public function createHeaderMenu($_, array $args, $context)
    {
        $service = app(HeaderService::class);

        $entry = $service->createMenu(
            $args['input']
        );

        $meta = $entry->metas->pluck(
            'meta_value',
            'meta_key'
        );

        $children = [];

        foreach ($entry->childEntries as $child) {

            $childMeta = $child->metas->pluck(
                'meta_value',
                'meta_key'
            );

            $children[] = [
                'id' => (string)$child->id,
                'label' => $childMeta['label'] ?? null,
                'href' => $childMeta['href'] ?? null,
            ];
        }

        return [
            'id' => (string)$entry->id,
            'label' => $meta['label'] ?? null,
            'children' => $children,
        ];
    }

    public function updateHeaderMenu($_, array $args)
    {
        $service = app(HeaderService::class);

        return $service->updateMenu(
            $args['id'],
            $args['input']
        );
    }

    public function deleteHeaderMenu($_, array $args)
    {
        $service = app(HeaderService::class);

        $service->deleteMenu(
            $args['id']
        );

        return [
            'message' => 'Menu deleted successfully'
        ];
    }
    public function updateLogo($_, array $args)
    {
        $service = app(HeaderService::class);

        return $service->updateLogo(
            $args['logo']
        );
    }

    public function updateCountdown($_, array $args)
    {
        $service = app(HeaderService::class);

        return $service->updateCountdown(
            $args['input']
        );
    }
    public function updateFavicon($_, array $args)
    {
        $service = app(HeaderService::class);

        return $service->updateFavicon(
            $args['favicon']
        );
    }
}
