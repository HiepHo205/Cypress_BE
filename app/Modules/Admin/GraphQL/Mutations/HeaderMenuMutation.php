<?php

namespace App\Modules\Admin\GraphQL\Mutations;

use App\Core\Services\Layout\Header\HeaderService;

class HeaderMenuMutation
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
}
