<?php

namespace App\Modules\Admin\GraphQL\Queries;

use App\Core\Models\Collection;

class FooterQuery
{
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
}
