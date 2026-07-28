<?php

namespace App\Modules\Admin\GraphQL\Queries;

use App\Core\Models\Collection;
use App\Core\Services\Layout\Footer\FooterService;

class FooterQuery
{
    public function __construct(
        protected FooterService $service
    ) {}

    public function footerBranding()
    {
        $entry = Collection::where('api_endpoint', 'footer')
            ->firstOrFail()
            ->entries()
            ->whereHas('metas', function ($query) {
                $query->where('meta_key', 'type')
                    ->where('meta_value', 'branding');
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
            'logo' => $meta->get('logo'),
            'company_name' => $meta->get('company_name'),
            'description' => $meta->get('description'),
        ];
    }

    public function socials()
    {
        $entry = Collection::where('api_endpoint', 'footer')
            ->firstOrFail()
            ->entries()
            ->whereHas('metas', function ($query) {
                $query->where('meta_key', 'type')
                    ->where('meta_value', 'social');
            })
            ->with('metas')
            ->first();

        if (!$entry) {
            return [];
        }

        $meta = $entry->metas
            ->pluck('meta_value', 'meta_key');

        $socials = json_decode(
            $meta->get('socials', '{}'),
            true
        );

        return collect($socials['socials'] ?? [])
            ->map(function ($item) {
                return [
                    'id' => $item['id'] ?? null,
                    'name' => $item['name'] ?? '',
                    'url' => $item['url'] ?? '',
                    'icon' => isset($item['icon']) && !is_array($item['icon'])
                        ? $item['icon']
                        : null,
                ];
            })
            ->values()
            ->toArray();
    }

    public function navigation()
    {
        return $this->service->getNavigation();
    }
    public function footer()
    {
        return $this->service->getFooter();
    }
    public function footerNewsletter()
    {
        return $this->service->getNewsletter();
    }
    public function footerBottomBar()
    {
        return $this->service->getBottomBar();
    }
}
