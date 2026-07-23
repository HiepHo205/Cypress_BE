<?php

namespace App\Modules\Admin\GraphQL\Queries;

use App\Core\Models\Collection;

class HeaderQuery
{
    public function header($_, array $args)
    {
        $collection = Collection::where(
            'api_endpoint',
            'header'
        )->firstOrFail();

        $entries = $collection
            ->entries()
            ->with([
                'metas',
                'childEntries.metas'
            ])
            ->where('status', 'published')
            ->get();


        $logo = null;
        $cta = null;
        $menus = [];


        foreach ($entries as $entry) {

            $meta = $entry->metas
                ->pluck('meta_value', 'meta_key');


            $type = $meta->get('type');


            if ($type === 'logo') {

                $logo = $meta->get('image');

                continue;
            }


            if ($type === 'cta') {

                $cta = [
                    'label' => $meta->get('label'),
                    'href' => $meta->get('href'),
                ];

                continue;
            }


            if ($type === 'menu') {

                $children = [];

                foreach ($entry->childEntries as $child) {

                    $childMeta = $child->metas
                        ->pluck('meta_value', 'meta_key');


                    $children[] = [
                        'id' => (string) $child->id,
                        'label' => $childMeta->get('label'),
                        'href' => $childMeta->get('href'),
                    ];
                }


                $menus[] = [
                    'id' => (string) $entry->id,
                    'label' => $meta->get('label'),
                    'children' => $children,
                ];
            }
        }


        return [
            'logo' => $logo,
            'menus' => $menus,
            'cta' => $cta,
        ];
    }
}
