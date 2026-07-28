<?php

namespace App\Modules\Admin\GraphQL\Mutations;

use App\Core\Services\Layout\Footer\FooterService;

class FooterMutation
{
    protected FooterService $service;

    public function __construct(FooterService $service)
    {
        $this->service = $service;
    }

    public function updateFooterBranding($_, array $args)
    {
        return $this->service->updateBranding(
            $args['input']
        );
    }

    public function updateFooterNewsletter($_, array $args)
    {
        return $this->service->updateNewsletter(
            $args['input']
        );
    }

    public function updateFooterSocial($_, array $args)
    {
        return $this->service->updateSocial(
            $args['input']
        );
    }

    public function updateFooterNavigation($_, array $args)
    {
        return $this->service->updateNavigation(
            $args['input']
        );
    }

    public function updateFooterBottomBar($_, array $args)
    {
        return $this->service->updateBottomBar(
            $args['input']
        );
    }
    public function footerNewsletter()
    {
        return $this->service->getNewsletter();
    }
}
