<?php

namespace App\Modules\Admin\GraphQL\Queries;

use App\Core\Services\CaseStudy\CaseStudyService;

class CaseStudyPageQuery
{
    public function __construct(
        private CaseStudyService $caseStudyPageService
    ) {}

    public function __invoke($_, array $args): array
    {
        return $this->caseStudyPageService->getCaseStudyPage();
    }
}
