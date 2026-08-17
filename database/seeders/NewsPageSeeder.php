<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NewsPageSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $this->seedNewsPage();
        });
    }

    private function seedNewsPage(): void
    {
        $collectionId = DB::table('collections')
            ->where('api_endpoint', 'cms')
            ->value('id');

        if (!$collectionId) {
            throw new \Exception('Collection CMS not found.');
        }

        $entry = DB::table('entries')
            ->where('collection_id', $collectionId)
            ->where('id', 72)
            ->first();

        if (!$entry) {
            throw new \Exception('CMS entry not found.');
        }

        $meta = DB::table('entry_meta')
            ->where('entry_id', $entry->id)
            ->where('meta_key', 'news_page')
            ->first();

        $newsPage = $meta
            ? json_decode($meta->meta_value, true)
            : [];

        if (!is_array($newsPage)) {
            $newsPage = [];
        }

        $newsPage['categories'] = [
            [
                'id' => 'category-fundraising',
                'title' => 'Fundraising',
                'description' => 'Fundraising',
            ],
            [
                'id' => 'category-gtm',
                'title' => 'GTM',
                'description' => 'GTM',
            ],
            [
                'id' => 'category-scaling',
                'title' => 'Scaling',
                'description' => 'Scaling',
            ],
            [
                'id' => 'category-engineering',
                'title' => 'Engineering',
                'description' => 'Engineering',
            ],
            [
                'id' => 'category-event',
                'title' => 'Event',
                'description' => 'Events and upcoming activities.',
            ],
        ];

        $newsPage['latest'] = [
            [
                'id' => 'news-001',
                'date' => 'August 24, 2025',
                'category' => 'Fundraising',
                'title' => 'GTM Strategies for 2024: How the Best Founders Find Their First 100 Customers',
                'description' => 'In the automotive industry, stainless steel is not just a common material; it has become a decisive factor in the durability, safety, and aesthetics of modern vehicles. With its corrosion resistance, ',
                'image' => [
                    'url' => 'https://res.cloudinary.com/droybexbj/image/upload/v1786527264/news/page/ean0dwhq2aaltwwqhqn0.jpg',
                    'public_id' => 'news/page/ean0dwhq2aaltwwqhqn0',
                ],
            ],
            [
                'id' => 'news-002',
                'date' => 'August 24, 2025',
                'category' => 'GTM',
                'title' => 'Applications of stainless steel in the modern automotive industry.',
                'description' => 'Discover how stainless steel contributes to durability, safety, performance, and modern automotive design.',
                'image' => [
                    'url' => 'https://res.cloudinary.com/droybexbj/image/upload/v1786527296/news/page/ld2imk3j52717wic5k0i.png',
                    'public_id' => 'news/page/ld2imk3j52717wic5k0i',
                ],
            ],
            [
                'id' => 'news-003',
                'date' => 'August 24, 2025',
                'category' => 'Scaling',
                'title' => 'Discover the role of stainless steel in the chemical industry.',
                'description' => 'Explore the important role of stainless steel in chemical processing and industrial applications.',
                'image' => [
                    'url' => 'https://res.cloudinary.com/droybexbj/image/upload/v1786527371/news/page/kj2ecjtowgy0uba0tt82.png',
                    'public_id' => 'news/page/kj2ecjtowgy0uba0tt82',
                ],
            ],
            [
                'id' => 'news-004',
                'date' => 'August 24, 2025',
                'category' => 'Engineering',
                'title' => 'Discover the role of stainless steel in the chemical industry.',
                'description' => 'Explore the important role of stainless steel in chemical processing and industrial applications.',
                'image' => [
                    'url' => 'https://res.cloudinary.com/droybexbj/image/upload/v1786527264/news/page/ean0dwhq2aaltwwqhqn0.jpg',
                    'public_id' => 'news/page/ean0dwhq2aaltwwqhqn0',
                ],
            ],
            [
                'id' => 'news-005',
                'date' => 'August 24, 2025',
                'category' => 'Event',
                'title' => 'Discover the role of stainless steel in the chemical industry.',
                'description' => 'Explore the important role of stainless steel in chemical processing and industrial applications.',
                'image' => [
                    'url' => 'https://res.cloudinary.com/droybexbj/image/upload/v1786527296/news/page/ld2imk3j52717wic5k0i.png',
                    'public_id' => 'news/page/ld2imk3j52717wic5k0i',
                ],
            ],
            [
                'id' => 'news-006',
                'date' => 'August 24, 2025',
                'category' => 'Fundraising',
                'title' => 'Discover the role of stainless steel in the chemical industry.',
                'description' => 'Explore the important role of stainless steel in chemical processing and industrial applications.',
                'image' => [
                    'url' => 'https://res.cloudinary.com/droybexbj/image/upload/v1786527371/news/page/kj2ecjtowgy0uba0tt82.png',
                    'public_id' => 'news/page/kj2ecjtowgy0uba0tt82',
                ],
            ],
            [
                'id' => 'news-007',
                'date' => 'August 24, 2025',
                'category' => 'GTM',
                'title' => 'Discover the role of stainless steel in the chemical industry.',
                'description' => 'Explore the important role of stainless steel in chemical processing and industrial applications.',
                'image' => [
                    'url' => 'https://res.cloudinary.com/droybexbj/image/upload/v1786527264/news/page/ean0dwhq2aaltwwqhqn0.jpg',
                    'public_id' => 'news/page/ean0dwhq2aaltwwqhqn0',
                ],
            ],
            [
                'id' => 'news-008',
                'date' => 'August 24, 2025',
                'category' => 'Event',
                'title' => 'Discover the role of stainless steel in the chemical industry.',
                'description' => 'Explore the important role of stainless steel in chemical processing and industrial applications.',
                'image' => [
                    'url' => 'https://res.cloudinary.com/droybexbj/image/upload/v1786527296/news/page/ld2imk3j52717wic5k0i.png',
                    'public_id' => 'news/page/ld2imk3j52717wic5k0i',
                ],
            ],
        ];

        $newsPage['featured'] = [
            [
                'id' => 'news-004',
                'date' => 'August 24, 2025',
                'category' => 'Event',
                'title' => 'Discover the role of stainless steel in the chemical industry.',
                'description' => 'Explore the important role of stainless steel in chemical processing and industrial applications.',
                'image' => [
                    'url' => 'https://res.cloudinary.com/droybexbj/image/upload/v1786527264/news/page/ean0dwhq2aaltwwqhqn0.jpg',
                    'public_id' => 'news/page/ean0dwhq2aaltwwqhqn0',
                ],
            ],

            [
                'id' => 'news-005',
                'date' => 'August 24, 2025',
                'category' => 'Event',
                'title' => 'Discover the role of stainless steel in the chemical industry.',
                'description' => 'Explore the important role of stainless steel in chemical processing and industrial applications.',
                'image' => [
                    'url' => 'https://res.cloudinary.com/droybexbj/image/upload/v1786527296/news/page/ld2imk3j52717wic5k0i.png',
                    'public_id' => 'news/page/ld2imk3j52717wic5k0i',
                ],
            ],

            [
                'id' => 'news-006',
                'date' => 'August 24, 2025',
                'category' => 'Event',
                'title' => 'Discover the role of stainless steel in the chemical industry.',
                'description' => 'Explore the important role of stainless steel in chemical processing and industrial applications.',
                'image' => [
                    'url' => 'https://res.cloudinary.com/droybexbj/image/upload/v1786527371/news/page/kj2ecjtowgy0uba0tt82.png',
                    'public_id' => 'news/page/kj2ecjtowgy0uba0tt82',
                ],
            ],

            [
                'id' => 'news-007',
                'date' => 'August 24, 2025',
                'category' => 'Event',
                'title' => 'Discover the role of stainless steel in the chemical industry.',
                'description' => 'Explore the important role of stainless steel in chemical processing and industrial applications.',
                'image' => [
                    'url' => 'https://res.cloudinary.com/droybexbj/image/upload/v1786527264/news/page/ean0dwhq2aaltwwqhqn0.jpg',
                    'public_id' => 'news/page/ean0dwhq2aaltwwqhqn0',
                ],
            ],

            [
                'id' => 'news-008',
                'date' => 'August 24, 2025',
                'category' => 'Event',
                'title' => 'Discover the role of stainless steel in the chemical industry.',
                'description' => 'Explore the important role of stainless steel in chemical processing and industrial applications.',
                'image' => [
                    'url' => 'https://res.cloudinary.com/droybexbj/image/upload/v1786527296/news/page/ld2imk3j52717wic5k0i.png',
                    'public_id' => 'news/page/ld2imk3j52717wic5k0i',
                ],
            ],

            [
                'id' => 'news-009',
                'date' => 'August 24, 2025',
                'category' => 'Event',
                'title' => 'Discover the role of stainless steel in the chemical industry.',
                'description' => 'Explore the important role of stainless steel in chemical processing and industrial applications.',
                'image' => [
                    'url' => 'https://res.cloudinary.com/droybexbj/image/upload/v1786527371/news/page/kj2ecjtowgy0uba0tt82.png',
                    'public_id' => 'news/page/kj2ecjtowgy0uba0tt82',
                ],
            ],

            [
                'id' => 'news-010',
                'date' => 'August 24, 2025',
                'category' => 'Event',
                'title' => 'Discover the role of stainless steel in the chemical industry.',
                'description' => 'Explore the important role of stainless steel in chemical processing and industrial applications.',
                'image' => [
                    'url' => 'https://res.cloudinary.com/droybexbj/image/upload/v1786527264/news/page/ean0dwhq2aaltwwqhqn0.jpg',
                    'public_id' => 'news/page/ean0dwhq2aaltwwqhqn0',
                ],
            ],

            [
                'id' => 'news-011',
                'date' => 'August 24, 2025',
                'category' => 'Event',
                'title' => 'Discover the role of stainless steel in the chemical industry.',
                'description' => 'Explore the important role of stainless steel in chemical processing and industrial applications.',
                'image' => [
                    'url' => 'https://res.cloudinary.com/droybexbj/image/upload/v1786527296/news/page/ld2imk3j52717wic5k0i.png',
                    'public_id' => 'news/page/ld2imk3j52717wic5k0i',
                ],
            ],
        ];
        $newsPage['banner'] = [
            'breadcrumb_first' => 'Home',
            'breadcrumb_first_url' => '/',
            'breadcrumb_second' => 'News',
            'breadcrumb_second_url' => '/news',
            'title' => 'The YC Events',
            'description' => 'Expert advice for every stage of your startup journey. From ideation and fundraising to scaling and GTM strategies.',
            'background_image' => [
                'url' => 'https://res.cloudinary.com/droybexbj/image/upload/v1786526583/news/page/banner/ojywu0baziyeqcsobzcv.jpg',
                'public_id' => 'news/page/banner/ojywu0baziyeqcsobzcv',
            ],
        ];

        $newsPage['newsletter'] = [
            'title1' => 'Latest News',
            'title2' => 'Featured Blogs',
            'title' => 'Subscribe To The YC Newsletter',
            'description' => "Join hundreds of founders who've turned their vision into reality with our support. Applications are open year-round.",
            'inputPlaceholder' => 'Enter your email',
            'buttonText' => 'Get Updates',
            'buttonUrl' => '/newsletter',
        ];

        DB::table('entry_meta')->updateOrInsert(
            [
                'entry_id' => $entry->id,
                'meta_key' => 'news_page',
            ],
            [
                'meta_value' => json_encode(
                    $newsPage,
                    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
                ),
                'updated_at' => now(),
                'created_at' => $meta
                    ? $meta->created_at
                    : now(),
            ]
        );
    }
}
