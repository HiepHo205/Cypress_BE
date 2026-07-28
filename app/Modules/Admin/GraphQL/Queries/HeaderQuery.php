<?php

namespace App\Modules\Admin\GraphQL\Queries;

use App\Core\Models\Collection;

class HeaderQuery
{
    public function header($_, array $args)
    {
        $collection = Collection::where('api_endpoint', 'header')
            ->firstOrFail();

        $entries = $collection
            ->entries()
            ->with([
                'metas',
                'childEntries.metas',
            ])
            ->where('status', 'published')
            ->get();

        $logo = null;
        $cta = null;
        $countdown = null;
        $menus = [];

        foreach ($entries as $entry) {

            $meta = $entry->metas->pluck('meta_value', 'meta_key');

            switch ($meta->get('type')) {

                case 'logo':
                    $logo = $meta->get('image');
                    break;

                case 'cta':
                    $cta = [
                        'label' => $meta->get('label'),
                        'href' => $meta->get('href'),
                    ];
                    break;

                case 'countdown':
                    $countdown = [
                        'enabled' => (bool) filter_var(
                            $meta->get('enabled', 1),
                            FILTER_VALIDATE_BOOLEAN
                        ),
                        'target_date' => $meta->get('target_date'),
                    ];
                    break;

                case 'menu':

                    $children = [];

                    foreach ($entry->childEntries as $child) {

                        $childMeta = $child->metas->pluck('meta_value', 'meta_key');

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

                    break;
            }
        }

        if ($countdown) {
            $countdown['button'] = [
                'label' => $cta['label'] ?? null,
                'href' => $cta['href'] ?? null,
            ];
        }

        return [
            'logo' => $logo,
            'menus' => $menus,
            'cta' => $cta,
            'countdown' => $countdown,
        ];
    }
    public function getLogo()
    {
        $entry = Collection::where('api_endpoint', 'header')
            ->firstOrFail()
            ->entries()
            ->whereHas('metas', function ($query) {
                $query->where('meta_key', 'type')
                    ->where('meta_value', 'logo');
            })
            ->with('metas')
            ->first();

        if (!$entry) {
            return null;
        }

        $meta = $entry->metas->pluck('meta_value', 'meta_key');

        return [
            'logo' => $meta->get('image'),
        ];
    }
    public function getFavicon()
    {
        $entry = Collection::where('api_endpoint', 'header')
            ->firstOrFail()
            ->entries()
            ->whereHas('metas', function ($query) {
                $query->where('meta_key', 'type')
                    ->where('meta_value', 'favicon');
            })
            ->with('metas')
            ->first();


        if (!$entry) {
            return null;
        }


        $meta = $entry->metas->pluck(
            'meta_value',
            'meta_key'
        );


        return [
            'url' => $meta['url'] ?? null,
            'public_id' => $meta['public_id'] ?? null,
        ];
    }
    public function getCta()
    {
        $entry = Collection::where('api_endpoint', 'header')
            ->firstOrFail()
            ->entries()
            ->whereHas('metas', function ($query) {
                $query->where('meta_key', 'type')
                    ->where('meta_value', 'cta');
            })
            ->with('metas')
            ->first();


        if (!$entry) {
            return null;
        }


        $meta = $entry->metas->pluck(
            'meta_value',
            'meta_key'
        );


        return [
            'label' => $meta->get('label'),
            'href' => $meta->get('href'),
        ];
    }
}
