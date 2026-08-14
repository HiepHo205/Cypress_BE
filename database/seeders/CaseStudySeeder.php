<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CaseStudySeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $collectionId = $this->getOrCreateCollection();

            $entryId = $this->getOrCreateEntry($collectionId);

            $this->seedCaseStudyPage($entryId);
        });
    }

    private function getOrCreateCollection(): int
    {
        $collection = DB::table('collections')
            ->where('api_endpoint', 'case-study')
            ->first();

        if ($collection) {
            return $collection->id;
        }

        return DB::table('collections')->insertGetId([
            'collection_name' => 'Case Study',
            'api_endpoint' => 'case-study',
            'is_system_type' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function getOrCreateEntry(int $collectionId): int
    {
        $entry = DB::table('entries')
            ->where('collection_id', $collectionId)
            ->first();

        if ($entry) {
            return $entry->id;
        }

        return DB::table('entries')->insertGetId([
            'collection_id' => $collectionId,
            'status' => 'published',
            'created_by' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function seedCaseStudyPage(int $entryId): void
    {
        $data = [
            'banner' => [
                'breadcrumb_first' => 'Home',
                'breadcrumb_first_url' => '/',
                'breadcrumb_second' => 'Case studies',
                'breadcrumb_second_url' => '/case-study',
                'title' => 'Unlocking Success: Our Client Transformation Stories',
                'description' => "Explore how we've empowered businesses across diverse sectors to achieve remarkable growth and overcome complex challenges.",
            ],

            'categories' => [
                [
                    'id' => 'category-01',
                    'title' => 'Category 1',
                    'children' => [
                        [
                            'id' => 'category-01-01',
                            'name' => 'Category 1.1',
                        ],
                        [
                            'id' => 'category-01-02',
                            'name' => 'Category 1.2',
                        ],
                        [
                            'id' => 'category-01-03',
                            'name' => 'Category 1.3',
                        ],
                    ],
                ],
                [
                    'id' => 'category-02',
                    'title' => 'Category 2',
                    'children' => [
                        [
                            'id' => 'category-02-01',
                            'name' => 'Category 2.1',
                        ],
                        [
                            'id' => 'category-02-02',
                            'name' => 'Category 2.2',
                        ],
                        [
                            'id' => 'category-02-03',
                            'name' => 'Category 2.3',
                        ],
                    ],
                ],
            ],

            'caseStudies' => $this->getCaseStudies(),
        ];

        DB::table('entry_meta')->updateOrInsert(
            [
                'entry_id' => $entryId,
                'meta_key' => 'case_study_page',
            ],
            [
                'meta_value' => json_encode(
                    $data,
                    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
                ),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    private function getCaseStudies(): array
    {
        $items = [];

        for ($i = 1; $i <= 15; $i++) {
            $id = $i === 1
                ? '54362672-fe58-46a8-8d46-4884ac996b18'
                : 'case-study-' . str_pad($i, 3, '0', STR_PAD_LEFT);

            $items[] = [
                'id' => $id,
                'title' => 'Lorem ipsum dolor sit amet consectetur.',
                'description' => 'Lorem ipsum dolor sit amet consectetur. Mattis consequat potenti tellus ac elit elementum ac...',
                'categories' => [
                    'Category 1.3',
                    'Category 2.2',
                ],
                'active' => true,
                'image' => [
                    'url' => 'https://res.cloudinary.com/droybexbj/image/upload/v1786615318/case-study/xwkkrz9cf3ehh3pkkpeo.jpg',
                    'public_id' => 'case-study/xwkkrz9cf3ehh3pkkpeo',
                ],
                'logo' => null,
            ];
        }

        return $items;
    }
}