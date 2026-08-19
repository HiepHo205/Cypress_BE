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
                'breadcrumb_first_url' => '/home',
                'breadcrumb_second' => 'Case studies',
                'breadcrumb_second_url' => '/case-studies',
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

            'caseStudyDetail' => $this->getQuangMinhInox(),

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
        $image = $this->image(
            'https://res.cloudinary.com/droybexbj/image/upload/v1786615318/case-study/xwkkrz9cf3ehh3pkkpeo.jpg',
            'case-study/xwkkrz9cf3ehh3pkkpeo'
        );

        $contentImage = $this->image(
            'https://res.cloudinary.com/droybexbj/image/upload/v1786935813/case-study/hkgt6xsnodviw8lmp3qv.png',
            'case-study/hkgt6xsnodviw8lmp3qv'
        );

        $socialIcon = $this->image(
            'https://res.cloudinary.com/droybexbj/image/upload/v1786900542/case-study/social/rstctgul3bl9bqomvkt4.png',
            'case-study/social/rstctgul3bl9bqomvkt4'
        );

        $items = [];

        $cards = [
            [
                'title' => 'Modern Brand Transformation',
                'date' => '2026-08-16',
                'planTitle' => 'BRAND GROWTH PLAN',
                'categories' => [
                    'Category 1.1',
                    'Category 2',
                ],
            ],
            [
                'title' => 'Digital Marketing Campaign',
                'date' => '2026-08-15',
                'planTitle' => 'GROWTH CAMPAIGN',
                'categories' => [
                    'Category 1.1',
                    'Category 2.1',
                ],
            ],
            [
                'title' => 'E-commerce Experience Upgrade',
                'date' => '2026-08-14',
                'planTitle' => 'E-COMMERCE PLAN',
                'categories' => [
                    'Category 1.1',
                    'Category 2',
                    'Category 2.1',
                ],
            ],
            [
                'title' => 'AI-Powered Business Solution',
                'date' => '2026-08-13',
                'planTitle' => 'AI TRANSFORMATION',
                'categories' => [
                    'Category 2',
                    'Category 2.1',
                ],
            ],
            [
                'title' => 'Lifestyle Brand Launch',
                'date' => '2026-08-12',
                'planTitle' => 'BRAND LAUNCH PLAN',
                'categories' => [
                    'Category 1.1',
                    'Category 2',
                ],
            ],
            [
                'title' => 'SaaS Product Growth',
                'date' => '2026-08-11',
                'planTitle' => 'SAAS GROWTH PLAN',
                'categories' => [
                    'Category 2',
                    'Category 2.1',
                ],
            ],
            [
                'title' => 'Social Media Growth Strategy',
                'date' => '2026-08-10',
                'planTitle' => 'SOCIAL GROWTH PLAN',
                'categories' => [
                    'Category 1.1',
                    'Category 2',
                ],
            ],
            [
                'title' => 'Technology Platform Redesign',
                'date' => '2026-08-09',
                'planTitle' => 'DIGITAL EXPERIENCE',
                'categories' => [
                    'Category 2',
                    'Category 2.1',
                ],
            ],
            [
                'title' => 'Content Strategy for Growth',
                'date' => '2026-08-08',
                'planTitle' => 'CONTENT GROWTH PLAN',
                'categories' => [
                    'Category 1.1',
                    'Category 2.1',
                ],
            ],
            [
                'title' => 'B2B Lead Generation Campaign',
                'date' => '2026-08-07',
                'planTitle' => 'B2B GROWTH PLAN',
                'categories' => [
                    'Category 1.1',
                    'Category 2',
                    'Category 2.1',
                ],
            ],
            [
                'title' => 'Creative Campaign Development',
                'date' => '2026-08-06',
                'planTitle' => 'CREATIVE CAMPAIGN',
                'categories' => [
                    'Category 1.1',
                    'Category 2',
                ],
            ],
            [
                'title' => 'Customer Experience Optimization',
                'date' => '2026-08-05',
                'planTitle' => 'CUSTOMER EXPERIENCE',
                'categories' => [
                    'Category 2',
                    'Category 2.1',
                ],
            ],
            [
                'title' => 'Startup Digital Transformation',
                'date' => '2026-08-04',
                'planTitle' => 'STARTUP GROWTH PLAN',
                'categories' => [
                    'Category 1.1',
                    'Category 2',
                    'Category 2.1',
                ],
            ],
            [
                'title' => 'Integrated Marketing Strategy',
                'date' => '2026-08-03',
                'planTitle' => 'MARKETING STRATEGY',
                'categories' => [
                    'Category 1.1',
                    'Category 2',
                ],
            ],
        ];

        foreach ($cards as $index => $card) {
            $items[] = $this->getCaseStudyDetail(
                'case-study-' . str_pad(
                    $index + 2,
                    3,
                    '0',
                    STR_PAD_LEFT
                ),
                $card['title'],
                $card['date'],
                $card['planTitle'],
                $card['categories'],
                $image,
                $contentImage,
                $socialIcon
            );
        }

        return $items;
    }
    private function getQuangMinhInox(): array
    {
        $image = $this->image(
            'https://res.cloudinary.com/droybexbj/image/upload/v1786615318/case-study/xwkkrz9cf3ehh3pkkpeo.jpg',
            'case-study/xwkkrz9cf3ehh3pkkpeo'
        );

        $contentImage = $this->image(
            'https://res.cloudinary.com/droybexbj/image/upload/v1786935813/case-study/hkgt6xsnodviw8lmp3qv.png',
            'case-study/hkgt6xsnodviw8lmp3qv'
        );

        $socialIcon = $this->image(
            'https://res.cloudinary.com/droybexbj/image/upload/v1786900542/case-study/social/rstctgul3bl9bqomvkt4.png',
            'case-study/social/rstctgul3bl9bqomvkt4'
        );

        return [
            'id' => 'case-study-001',

            'title' => 'Quang Minh Inox',

            'description' => 'Lorem ipsum dolor sit amet consectetur. Ipsum aliquam odio eget lacus viverra.',

            'categories' => [
                'Category 1.3',
                'Category 2.2',
            ],

            'active' => true,

            'date' => '2026-08-17',

            'author' => 'Admin',

            'clientName' => 'Quang Minh Inox',

            'planTitle' => 'SERIES A PLAN',

            'seriesTags' => [
                'Marketing',
                'Lifestyle',
                'AI Agent',
                'Software',
            ],
            'tableOfContents' => [
                [
                    'id' => 'toc-1',
                    'order' => 1,
                    'title' => 'Lorem ipsum dolor sit amet consectetur. Ipsum aliquam odio eget lacus viverra',
                    'children' => [
                        'Accumsan ligula eu dui ante justo eu sit mus.',
                        'Nulla nunc pharetra ut porta facilisi lacus id consequat.',
                    ],
                ],

                [
                    'id' => 'toc-2',
                    'order' => 2,
                    'title' => 'Lorem ipsum dolor sit amet consectetur. Ipsum aliquam odio eget lacus viverra',
                    'children' => [],
                ],

                [
                    'id' => 'toc-3',
                    'order' => 3,
                    'title' => 'Lorem ipsum dolor sit amet consectetur. Ipsum aliquam odio eget lacus viverra',
                    'children' => [],
                ],
            ],
            'sections' => [

                [
                    'id' => 'content-1',
                    'type' => 'text',
                    'title' => 'Lorem ipsum dolor sit amet consectetur. Ipsum aliquam odio eget lacus viverra',
                    'content' => 'Lorem ipsum dolor sit amet consectetur. Interdum elit eget morbi scelerisque. Facilisis sagittis aliquet nisl pharetra turpis eu et eget et. Placerat aliquam ac amet sit amet egestas proin. Nisl proin quam et hac nibh nibh ut. Eu ac sed eu et erat tincidunt pellentesque potenti. Vestibulum sed vel donec felis eleifend in leo. Dapibus erat ullamcorper aliquet enim diam dignissim laoreet amet. At sollicitudin malesuada id tristique sed orci in tellus. Quam est vulputate ante augue. Non sem viverra adipiscing diam et lacus pharetra pellentesque vel. In neque ac maecenas augue morbi pulvinar. Semper scelerisque viverra iaculis at in in.',
                    'order' => 1,
                ],

                [
                    'id' => 'content-1-1',
                    'type' => 'text',
                    'title' => 'Accumsan ligula eu dui ante justo eu sit mus.',
                    'content' => 'Proin sed tortor aliquet integer vel ipsum odio. Justo nisl felis felis a senectus. Neque ac mattis neque massa aliquam quam. Enim posuere eget massa at amet cursus. Condimentum convallis et bibendum quis. Sit tellus nulla enim id. Amet egestas dignissim diam purus mi viverra sodales vitae. Aliquet ut praesent commodo tortor integer diam dictum elementum posuere. Ullamcorper etiam parturient sagittis hac. Eget odio enim quisque facilisis. Lectus quam ut dignissim magna proin sed mattis vestibulum tellus. Quam facilisi gravida suspendisse at leo eu donec sit non.',
                    'order' => 2,
                ],

                [
                    'id' => 'content-1-2',
                    'type' => 'text',
                    'title' => 'Nulla nunc pharetra ut porta facilisi lacus id consequat.',
                    'content' => 'Imperdiet consectetur bibendum eget cras eget sit non egestas in. Magna pretium feugiat sodales porttitor ultrices. Viverra in eget nunc imperdiet accumsan. Massa eu metus vitae magna aliquam vitae justo. In enim vivamus lorem fames dui aliquet lectus vitae. Consectetur ultrices malesuada risus pharetra facilisis. Ac gravida tincidunt quam tellus tortor vel. Commodo leo faucibus semper tellus id lectus lorem duis magnis. Arcu sollicitudin consectetur est et vitae nulla aliquam vitae. Convallis fames maecenas pellentesque amet viverra id quam habitant enim. Ut diam non ultricies pellentesque quis porta nibh. Enim sed nisl tincidunt sem. Sed at volutpat pharetra purus.',
                    'image' => $contentImage,
                    'order' => 3,
                ],


                [
                    'id' => 'content-2',
                    'type' => 'text',
                    'title' => 'Lorem ipsum dolor sit amet consectetur. Ipsum aliquam odio eget lacus viverra',
                    'content' => 'Lorem ipsum dolor sit amet consectetur. Interdum elit eget morbi scelerisque. Facilisis sagittis aliquet nisl pharetra turpis eu et eget et. Placerat aliquam ac amet sit amet egestas proin. Nisl proin quam et hac nibh nibh ut. Eu ac sed eu et erat tincidunt pellentesque potenti. Vestibulum sed vel donec felis eleifend in leo. Dapibus erat ullamcorper aliquet enim diam dignissim laoreet amet. At sollicitudin malesuada id tristique sed orci in tellus. Quam est vulputate ante augue. Non sem viverra adipiscing diam et lacus pharetra pellentesque vel. In neque ac maecenas augue morbi pulvinar. Semper scelerisque viverra iaculis at in in.',
                    'order' => 4,
                ],

                [
                    'id' => 'content-2-1',
                    'type' => 'text',
                    'title' => '',
                    'content' => 'Accumsan ligula eu dui ante justo eu sit mus. Proin sed tortor aliquet integer vel ipsum odio. Justo nisl felis felis a senectus. Neque ac mattis neque massa aliquam quam. Enim posuere eget massa at amet cursus. Condimentum convallis et bibendum quis. Sit tellus nulla enim id. Amet egestas dignissim diam purus mi viverra sodales vitae. Aliquet ut praesent commodo tortor integer diam dictum elementum posuere. Ullamcorper etiam parturient sagittis hac. Eget odio enim quisque facilisis. Lectus quam ut dignissim magna proin sed mattis vestibulum tellus. Quam facilisi gravida suspendisse at leo eu donec sit non.',
                    'order' => 5,
                ],

                [
                    'id' => 'content-2-2',
                    'type' => 'text',
                    'title' => '',
                    'content' => 'Nulla nunc pharetra ut porta facilisi lacus id consequat. Imperdiet consectetur bibendum eget cras eget sit non egestas in. Magna pretium feugiat sodales porttitor ultrices. Viverra in eget nunc imperdiet accumsan. Massa eu metus vitae magna aliquam vitae justo. In enim vivamus lorem fames dui aliquet lectus vitae. Consectetur ultrices malesuada risus pharetra facilisis. Ac gravida tincidunt quam tellus tortor vel. Commodo leo faucibus semper tellus id lectus lorem duis magnis. Arcu sollicitudin consectetur est et vitae nulla aliquam vitae. Convallis fames maecenas pellentesque amet viverra id quam habitant enim. Ut diam non ultricies pellentesque quis porta nibh. Enim sed nisl tincidunt sem. Sed at volutpat pharetra purus.',
                    'order' => 6,
                ],


                [
                    'id' => 'content-3',
                    'type' => 'text',
                    'title' => 'Lorem ipsum dolor sit amet consectetur. Ipsum aliquam odio eget lacus viverra',
                    'content' => 'Lorem ipsum dolor sit amet consectetur. Interdum elit eget morbi scelerisque. Facilisis sagittis aliquet nisl pharetra turpis eu et eget et. Placerat aliquam ac amet sit amet egestas proin. Nisl proin quam et hac nibh nibh ut. Eu ac sed eu et erat tincidunt pellentesque potenti. Vestibulum sed vel donec felis eleifend in leo. Dapibus erat ullamcorper aliquet enim diam dignissim laoreet amet. At sollicitudin malesuada id tristique sed orci in tellus. Quam est vulputate ante augue. Non sem viverra adipiscing diam et lacus pharetra pellentesque vel. In neque ac maecenas augue morbi pulvinar. Semper scelerisque viverra iaculis at in in.',
                    'order' => 7,
                ],
            ],

            'image' => $image,

            'logo' => $image,

            'social_media' => [
                [
                    'name' => 'facebook',
                    'icon' => $this->image(
                        'https://cdn.simpleicons.org/facebook',
                        'social/facebook'
                    ),
                    'url' => 'https://www.facebook.com/?locale=vi_VN',
                ],

                [
                    'name' => 'instagram',
                    'icon' => $this->image(
                        'https://cdn.simpleicons.org/instagram',
                        'social/instagram'
                    ),
                    'url' => 'https://www.instagram.com/',
                ],

                [
                    'name' => 'linkedin',
                    'icon' => $this->image(
                        'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS4tGcxJQXhAvXcn0CgP-jXIlEfsb6SXUBP2uckhKF1e0Q3iW2hj1bMDj4&s=10',
                        'social/linkedin'
                    ),
                    'url' => 'https://www.linkedin.com/',
                ],

                [
                    'name' => 'x',
                    'icon' => $this->image(
                        'https://cdn.simpleicons.org/x',
                        'social/x'
                    ),
                    'url' => 'https://x.com/',
                ],

                [
                    'name' => 'pinterest',
                    'icon' => $this->image(
                        'https://cdn.simpleicons.org/pinterest',
                        'social/pinterest'
                    ),
                    'url' => 'https://www.pinterest.com/',
                ],
            ],
        ];
    }

    private function getCaseStudyDetail(
        string $id,
        string $title,
        string $date,
        string $planTitle,
        array $categories,
        array $image,
        array $contentImage,
        array $socialIcon
    ): array {
        return [
            'id' => $id,

            'title' => $title,

            'description' => 'Lorem ipsum dolor sit amet consectetur. Ipsum aliquam odio eget lacus viverra.',

            'categories' => $categories,

            'active' => true,

            'date' => $date,

            'author' => 'Admin',

            'clientName' => $title,

            'planTitle' => $planTitle,

            'seriesTags' => [
                'Marketing',
                'Lifestyle',
                'AI Agent',
                'Software',
            ],

            'tableOfContents' => [
                [
                    'id' => $id . '-toc-1',
                    'order' => 1,
                    'title' => 'Lorem ipsum dolor sit amet consectetur. Ipsum aliquam odio eget lacus viverra',
                    'children' => [
                        'Accumsan ligula eu dui ante justo eu sit mus.',
                        'Nulla nunc pharetra ut porta facilisi lacus id consequat.',
                    ],
                ],

                [
                    'id' => $id . '-toc-2',
                    'order' => 2,
                    'title' => 'Lorem ipsum dolor sit amet consectetur. Ipsum aliquam odio eget lacus viverra',
                    'children' => [],
                ],

                [
                    'id' => $id . '-toc-3',
                    'order' => 3,
                    'title' => 'Lorem ipsum dolor sit amet consectetur. Ipsum aliquam odio eget lacus viverra',
                    'children' => [],
                ],
            ],

            'sections' => [

                [
                    'id' => $id . '-content-1',
                    'type' => 'text',
                    'title' => 'Lorem ipsum dolor sit amet consectetur. Ipsum aliquam odio eget lacus viverra',
                    'content' => 'Lorem ipsum dolor sit amet consectetur. Interdum elit eget morbi scelerisque. Facilisis sagittis aliquet nisl pharetra turpis eu et eget et. Placerat aliquam ac amet sit amet egestas proin. Nisl proin quam et hac nibh nibh ut. Eu ac sed eu et erat tincidunt pellentesque potenti. Vestibulum sed vel donec felis eleifend in leo. Dapibus erat ullamcorper aliquet enim diam dignissim laoreet amet. At sollicitudin malesuada id tristique sed orci in tellus. Quam est vulputate ante augue. Non sem viverra adipiscing diam et lacus pharetra pellentesque vel. In neque ac maecenas augue morbi pulvinar. Semper scelerisque viverra iaculis at in in.',
                    'order' => 1,
                ],

                [
                    'id' => $id . '-content-1-1',
                    'type' => 'text',
                    'title' => 'Accumsan ligula eu dui ante justo eu sit mus.',
                    'content' => 'Proin sed tortor aliquet integer vel ipsum odio. Justo nisl felis felis a senectus. Neque ac mattis neque massa aliquam quam. Enim posuere eget massa at amet cursus. Condimentum convallis et bibendum quis. Sit tellus nulla enim id. Amet egestas dignissim diam purus mi viverra sodales vitae. Aliquet ut praesent commodo tortor integer diam dictum elementum posuere. Ullamcorper etiam parturient sagittis hac. Eget odio enim quisque facilisis. Lectus quam ut dignissim magna proin sed mattis vestibulum tellus. Quam facilisi gravida suspendisse at leo eu donec sit non.',
                    'order' => 2,
                ],


                [
                    'id' => $id . '-content-1-2',
                    'type' => 'text',
                    'title' => 'Nulla nunc pharetra ut porta facilisi lacus id consequat.',
                    'content' => 'Imperdiet consectetur bibendum eget cras eget sit non egestas in. Magna pretium feugiat sodales porttitor ultrices. Viverra in eget nunc imperdiet accumsan. Massa eu metus vitae magna aliquam vitae justo. In enim vivamus lorem fames dui aliquet lectus vitae. Consectetur ultrices malesuada risus pharetra facilisis. Ac gravida tincidunt quam tellus tortor vel. Commodo leo faucibus semper tellus id lectus lorem duis magnis. Arcu sollicitudin consectetur est et vitae nulla aliquam vitae. Convallis fames maecenas pellentesque amet viverra id quam habitant enim. Ut diam non ultricies pellentesque quis porta nibh. Enim sed nisl tincidunt sem. Sed at volutpat pharetra purus.',
                    'image' => $contentImage,
                    'order' => 3,
                ],

                [
                    'id' => $id . '-content-2',
                    'type' => 'text',
                    'title' => 'Lorem ipsum dolor sit amet consectetur. Ipsum aliquam odio eget lacus viverra',
                    'content' => 'Lorem ipsum dolor sit amet consectetur. Interdum elit eget morbi scelerisque. Facilisis sagittis aliquet nisl pharetra turpis eu et eget et. Placerat aliquam ac amet sit amet egestas proin. Nisl proin quam et hac nibh nibh ut. Eu ac sed eu et erat tincidunt pellentesque potenti. Vestibulum sed vel donec felis eleifend in leo. Dapibus erat ullamcorper aliquet enim diam dignissim laoreet amet. At sollicitudin malesuada id tristique sed orci in tellus. Quam est vulputate ante augue. Non sem viverra adipiscing diam et lacus pharetra pellentesque vel. In neque ac maecenas augue morbi pulvinar. Semper scelerisque viverra iaculis at in in.',
                    'order' => 4,
                ],

                [
                    'id' => $id . '-content-2-1',
                    'type' => 'text',
                    'title' => '',
                    'content' => 'Accumsan ligula eu dui ante justo eu sit mus. Proin sed tortor aliquet integer vel ipsum odio. Justo nisl felis felis a senectus. Neque ac mattis neque massa aliquam quam. Enim posuere eget massa at amet cursus. Condimentum convallis et bibendum quis. Sit tellus nulla enim id. Amet egestas dignissim diam purus mi viverra sodales vitae. Aliquet ut praesent commodo tortor integer diam dictum elementum posuere. Ullamcorper etiam parturient sagittis hac. Eget odio enim quisque facilisis. Lectus quam ut dignissim magna proin sed mattis vestibulum tellus. Quam facilisi gravida suspendisse at leo eu donec sit non.',
                    'order' => 5,
                ],

                [
                    'id' => $id . '-content-2-2',
                    'type' => 'text',
                    'title' => '',
                    'content' => 'Nulla nunc pharetra ut porta facilisi lacus id consequat. Imperdiet consectetur bibendum eget cras eget sit non egestas in. Magna pretium feugiat sodales porttitor ultrices. Viverra in eget nunc imperdiet accumsan. Massa eu metus vitae magna aliquam vitae justo. In enim vivamus lorem fames dui aliquet lectus vitae. Consectetur ultrices malesuada risus pharetra facilisis. Ac gravida tincidunt quam tellus tortor vel. Commodo leo faucibus semper tellus id lectus lorem duis magnis. Arcu sollicitudin consectetur est et vitae nulla aliquam vitae. Convallis fames maecenas pellentesque amet viverra id quam habitant enim. Ut diam non ultricies pellentesque quis porta nibh. Enim sed nisl tincidunt sem. Sed at volutpat pharetra purus.',
                    'order' => 6,
                ],
                [
                    'id' => $id . '-content-3',
                    'type' => 'text',
                    'title' => 'Lorem ipsum dolor sit amet consectetur. Ipsum aliquam odio eget lacus viverra',
                    'content' => 'Lorem ipsum dolor sit amet consectetur. Interdum elit eget morbi scelerisque. Facilisis sagittis aliquet nisl pharetra turpis eu et eget et. Placerat aliquam ac amet sit amet egestas proin. Nisl proin quam et hac nibh nibh ut. Eu ac sed eu et erat tincidunt pellentesque potenti. Vestibulum sed vel donec felis eleifend in leo. Dapibus erat ullamcorper aliquet enim diam dignissim laoreet amet. At sollicitudin malesuada id tristique sed orci in tellus. Quam est vulputate ante augue. Non sem viverra adipiscing diam et lacus pharetra pellentesque vel. In neque ac maecenas augue morbi pulvinar. Semper scelerisque viverra iaculis at in in.',
                    'order' => 7,
                ],
            ],

            'image' => $image,

            'logo' => $image,

            'social_media' => [
                [
                    'name' => 'facebook',
                    'icon' => $this->image(
                        'https://cdn.simpleicons.org/facebook',
                        'social/facebook'
                    ),
                    'url' => 'https://www.facebook.com/?locale=vi_VN',
                ],

                [
                    'name' => 'instagram',
                    'icon' => $this->image(
                        'https://cdn.simpleicons.org/instagram',
                        'social/instagram'
                    ),
                    'url' => 'https://www.instagram.com/',
                ],

                [
                    'name' => 'linkedin',
                    'icon' => $this->image(
                        'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS4tGcxJQXhAvXcn0CgP-jXIlEfsb6SXUBP2uckhKF1e0Q3iW2hj1bMDj4&s=10',
                        'social/linkedin'
                    ),
                    'url' => 'https://www.linkedin.com/',
                ],

                [
                    'name' => 'x',
                    'icon' => $this->image(
                        'https://cdn.simpleicons.org/x',
                        'social/x'
                    ),
                    'url' => 'https://x.com/',
                ],

                [
                    'name' => 'pinterest',
                    'icon' => $this->image(
                        'https://cdn.simpleicons.org/pinterest',
                        'social/pinterest'
                    ),
                    'url' => 'https://www.pinterest.com/',
                ],
            ],
        ];
    }

    private function image(string $url, string $publicId): array
    {
        return [
            'url' => $url,
            'public_id' => $publicId,
        ];
    }
}
