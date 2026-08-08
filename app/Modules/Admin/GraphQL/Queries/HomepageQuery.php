<?php

namespace App\Modules\Admin\GraphQL\Queries;

use App\Core\Services\Home\HomepageService;

class HomepageQuery
{
    protected HomepageService $homepageService;


    public function __construct(
        HomepageService $homepageService
    ) {
        $this->homepageService = $homepageService;
    }


    public function homepage()
    {
        return $this->homepageService->getHomepage();
    }


    public function homepageSection(
        $_,
        array $args
    ) {

        return $this->homepageService->getSection(
            $args['section']
        );
    }
}
