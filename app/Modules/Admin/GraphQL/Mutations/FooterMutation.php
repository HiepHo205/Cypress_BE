<?php

namespace App\Modules\Admin\GraphQL\Mutations;

use App\Core\Services\Layout\Footer\FooterService;

class FooterMutation
{
    public function updateFooterBranding($_, array $args)
    {
        $service = app(FooterService::class);

        return $service->updateBranding(
            $args['input']
        );
    }
}
