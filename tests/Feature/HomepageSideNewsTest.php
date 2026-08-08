<?php

namespace Tests\Feature;

use App\Core\Repositories\Eloquent\HomepageRepository;
use App\Core\Services\Home\HomepageService;
use Tests\TestCase;

class HomepageSideNewsTest extends TestCase
{
    public function test_update_side_news_appends_new_item_without_replacing_existing_ones(): void
    {
        $repository = app(HomepageRepository::class);
        $service = app(HomepageService::class);

        $repository->updateSection('side_news', [
            'sideNews' => [
                [
                    'id' => '1',
                    'description' => 'Old side news',
                ],
            ],
        ]);

        $service->updateSection('sideNews', [
            'sideNews' => [
                [
                    'id' => null,
                    'description' => 'New side news',
                ],
            ],
        ]);

        $data = $repository->getSection('side_news');

        $this->assertCount(2, $data['sideNews']);
        $this->assertSame('Old side news', $data['sideNews'][0]['description']);
        $this->assertSame('New side news', $data['sideNews'][1]['description']);
        $this->assertNotNull($data['sideNews'][1]['id']);
    }
}
