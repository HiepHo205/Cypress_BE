<?php

namespace App\Modules\Admin\GraphQL\Queries;

use App\Core\Services\News\NewsPageService;

;

class NewsPageQuery
{
    public function __construct(
        private NewsPageService $newsPageService
    ) {}

    public function __invoke($_, array $args): array
    {
        return $this->newsPageService->getNewsPage();
    }
}
