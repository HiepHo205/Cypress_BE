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

        $news = $this->getNews();

        $newsPage['latest'] = array_values(
            array_filter(
                $news,
                fn (array $item) => in_array(
                    $item['id'],
                    [
                        'news-001',
                        'news-002',
                        'news-003',
                        'news-004',
                        'news-005',
                        'news-006',
                        'news-007',
                        'news-008',
                    ],
                    true
                )
            )
        );

        $newsPage['featured'] = array_values(
            array_filter(
                $news,
                fn (array $item) => in_array(
                    $item['id'],
                    [
                        'news-004',
                        'news-005',
                        'news-006',
                        'news-007',
                        'news-008',
                        'news-009',
                        'news-010',
                        'news-011',
                    ],
                    true
                )
            )
        );

        $newsPage['banner'] = [
            'breadcrumb_first' => 'Home',
            'breadcrumb_first_url' => '/',
            'breadcrumb_second' => 'News',
            'breadcrumb_second_url' => '/news',
            'title' => 'The YC Events',
            'description' => 'Expert advice for every stage of your startup journey. From ideation and fundraising to scaling and GTM strategies.',
            'background_image' => $this->image(
                'https://res.cloudinary.com/droybexbj/image/upload/v1786526583/news/page/banner/ojywu0baziyeqcsobzcv.jpg',
                'news/page/banner/ojywu0baziyeqcsobzcv'
            ),
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

    private function getNews(): array
    {
        $items = [
            [
                'id' => 'news-001',
                'date' => 'August 24, 2025',
                'category' => 'Fundraising',
                'title' => 'GTM Strategies for 2024: How the Best Founders Find Their First 100 Customers',
                'description' => 'In the automotive industry, stainless steel is not just a common material; it has become a decisive factor in the durability, safety, and aesthetics of modern vehicles.',
                'image' => $this->image(
                    'https://res.cloudinary.com/droybexbj/image/upload/v1786527264/news/page/ean0dwhq2aaltwwqhqn0.jpg',
                    'news/page/ean0dwhq2aaltwwqhqn0'
                ),
            ],
            [
                'id' => 'news-002',
                'date' => 'August 24, 2025',
                'category' => 'GTM',
                'title' => 'Applications of stainless steel in the modern automotive industry.',
                'description' => 'Discover how stainless steel contributes to durability, safety, performance, and modern automotive design.',
                'image' => $this->image(
                    'https://res.cloudinary.com/droybexbj/image/upload/v1786527296/news/page/ld2imk3j52717wic5k0i.png',
                    'news/page/ld2imk3j52717wic5k0i'
                ),
            ],
            [
                'id' => 'news-003',
                'date' => 'August 24, 2025',
                'category' => 'Scaling',
                'title' => 'Discover the role of stainless steel in the chemical industry.',
                'description' => 'Explore the important role of stainless steel in chemical processing and industrial applications.',
                'image' => $this->image(
                    'https://res.cloudinary.com/droybexbj/image/upload/v1786527371/news/page/kj2ecjtowgy0uba0tt82.png',
                    'news/page/kj2ecjtowgy0uba0tt82'
                ),
            ],
            [
                'id' => 'news-004',
                'date' => 'August 24, 2025',
                'category' => 'Engineering',
                'title' => 'Discover the role of stainless steel in the chemical industry.',
                'description' => 'Explore the important role of stainless steel in chemical processing and industrial applications.',
                'image' => $this->image(
                    'https://res.cloudinary.com/droybexbj/image/upload/v1786527264/news/page/ean0dwhq2aaltwwqhqn0.jpg',
                    'news/page/ean0dwhq2aaltwwqhqn0'
                ),
            ],
            [
                'id' => 'news-005',
                'date' => 'August 24, 2025',
                'category' => 'Event',
                'title' => 'Discover the role of stainless steel in the chemical industry.',
                'description' => 'Explore how stainless steel improves durability and performance in modern industrial systems.',
                'image' => $this->image(
                    'https://res.cloudinary.com/droybexbj/image/upload/v1786527296/news/page/ld2imk3j52717wic5k0i.png',
                    'news/page/ld2imk3j52717wic5k0i'
                ),
            ],
            [
                'id' => 'news-006',
                'date' => 'August 24, 2025',
                'category' => 'Fundraising',
                'title' => 'Discover the role of stainless steel in the chemical industry.',
                'description' => 'Explore the important role of stainless steel in chemical processing and industrial applications.',
                'image' => $this->image(
                    'https://res.cloudinary.com/droybexbj/image/upload/v1786527371/news/page/kj2ecjtowgy0uba0tt82.png',
                    'news/page/kj2ecjtowgy0uba0tt82'
                ),
            ],
            [
                'id' => 'news-007',
                'date' => 'August 24, 2025',
                'category' => 'GTM',
                'title' => 'Discover the role of stainless steel in modern industrial applications.',
                'description' => 'Explore how stainless steel contributes to durability, safety, and modern industrial applications.',
                'image' => $this->image(
                    'https://res.cloudinary.com/droybexbj/image/upload/v1786527264/news/page/ean0dwhq2aaltwwqhqn0.jpg',
                    'news/page/ean0dwhq2aaltwwqhqn0'
                ),
            ],
            [
                'id' => 'news-008',
                'date' => 'August 24, 2025',
                'category' => 'Event',
                'title' => 'Discover the role of stainless steel in industrial systems.',
                'description' => 'Learn how stainless steel supports reliable and efficient industrial operations.',
                'image' => $this->image(
                    'https://res.cloudinary.com/droybexbj/image/upload/v1786527296/news/page/ld2imk3j52717wic5k0i.png',
                    'news/page/ld2imk3j52717wic5k0i'
                ),
            ],
            [
                'id' => 'news-009',
                'date' => 'August 24, 2025',
                'category' => 'Event',
                'title' => 'Stainless steel solutions for modern engineering.',
                'description' => 'Discover reliable stainless steel solutions for modern engineering and manufacturing.',
                'image' => $this->image(
                    'https://res.cloudinary.com/droybexbj/image/upload/v1786527371/news/page/kj2ecjtowgy0uba0tt82.png',
                    'news/page/kj2ecjtowgy0uba0tt82'
                ),
            ],
            [
                'id' => 'news-010',
                'date' => 'August 24, 2025',
                'category' => 'Event',
                'title' => 'The future of stainless steel in industrial technology.',
                'description' => 'Explore the future applications of stainless steel in technology and industrial systems.',
                'image' => $this->image(
                    'https://res.cloudinary.com/droybexbj/image/upload/v1786527264/news/page/ean0dwhq2aaltwwqhqn0.jpg',
                    'news/page/ean0dwhq2aaltwwqhqn0'
                ),
            ],
            [
                'id' => 'news-011',
                'date' => 'August 24, 2025',
                'category' => 'Event',
                'title' => 'Integrated stainless steel solutions for modern business.',
                'description' => 'Explore integrated stainless steel solutions designed for modern industrial businesses.',
                'image' => $this->image(
                    'https://res.cloudinary.com/droybexbj/image/upload/v1786527296/news/page/ld2imk3j52717wic5k0i.png',
                    'news/page/ld2imk3j52717wic5k0i'
                ),
            ],
        ];

        return array_map(
            fn (array $item) => $this->getNewsDetail($item),
            $items
        );
    }

    private function getNewsDetail(array $item): array
    {
        $id = $item['id'];

        $contentImage = $this->image(
            'https://res.cloudinary.com/droybexbj/image/upload/v1786935813/case-study/hkgt6xsnodviw8lmp3qv.png',
            'news/content/hkgt6xsnodviw8lmp3qv'
        );

        return [
            'id' => $id,
            'title' => $item['title'],
            'description' => $item['description'],

            'categories' => [
                $item['category'],
            ],

            'category' => $item['category'],

            'active' => true,

            'featured' => in_array(
                $id,
                [
                    'news-004',
                    'news-005',
                    'news-006',
                    'news-007',
                    'news-008',
                    'news-009',
                    'news-010',
                    'news-011',
                ],
                true
            ),

            'date' => $item['date'],

            'author' => 'Admin',

            'image' => $item['image'],

            'logo' => $item['image'],

            'tableOfContents' => [
                [
                    'id' => $id . '-toc-1',
                    'order' => 1,
                    'title' => 'Introduction and Business Context',
                    'children' => [
                        'Market Overview',
                        'Business Challenge',
                    ],
                ],
                [
                    'id' => $id . '-toc-2',
                    'order' => 2,
                    'title' => 'Strategy and Implementation',
                    'children' => [
                        'Strategic Approach',
                        'Implementation Process',
                    ],
                ],
                [
                    'id' => $id . '-toc-3',
                    'order' => 3,
                    'title' => 'Results and Future Direction',
                    'children' => [],
                ],
            ],

            'sections' => [
                [
                    'id' => $id . '-content-1',
                    'type' => 'text',
                    'title' => 'Introduction and Business Context',
                    'content' => 'Lorem ipsum dolor sit amet consectetur. Interdum elit eget morbi scelerisque. Facilisis sagittis aliquet nisl pharetra turpis eu et eget et. Placerat aliquam ac amet sit amet egestas proin. Nisl proin quam et hac nibh nibh ut. Eu ac sed eu et erat tincidunt pellentesque potenti. Vestibulum sed vel donec felis eleifend in leo. Dapibus erat ullamcorper aliquet enim diam dignissim laoreet amet.',
                    'order' => 1,
                ],
                [
                    'id' => $id . '-content-1-1',
                    'type' => 'text',
                    'title' => 'Market Overview',
                    'content' => 'Proin sed tortor aliquet integer vel ipsum odio. Justo nisl felis felis a senectus. Neque ac mattis neque massa aliquam quam. Enim posuere eget massa at amet cursus. Condimentum convallis et bibendum quis. Sit tellus nulla enim id.',
                    'order' => 2,
                ],
                [
                    'id' => $id . '-content-1-2',
                    'type' => 'text',
                    'title' => 'Business Challenge',
                    'content' => 'Imperdiet consectetur bibendum eget cras eget sit non egestas in. Magna pretium feugiat sodales porttitor ultrices. Viverra in eget nunc imperdiet accumsan. Massa eu metus vitae magna aliquam vitae justo. In enim vivamus lorem fames dui aliquet lectus vitae.',
                    'image' => $contentImage,
                    'order' => 3,
                ],
                [
                    'id' => $id . '-content-2',
                    'type' => 'text',
                    'title' => 'Strategy and Implementation',
                    'content' => 'Lorem ipsum dolor sit amet consectetur. Interdum elit eget morbi scelerisque. Facilisis sagittis aliquet nisl pharetra turpis eu et eget et. Placerat aliquam ac amet sit amet egestas proin. Nisl proin quam et hac nibh nibh ut.',
                    'order' => 4,
                ],
                [
                    'id' => $id . '-content-2-1',
                    'type' => 'text',
                    'title' => 'Strategic Approach',
                    'content' => 'Accumsan ligula eu dui ante justo eu sit mus. Proin sed tortor aliquet integer vel ipsum odio. Justo nisl felis felis a senectus. Neque ac mattis neque massa aliquam quam. Enim posuere eget massa at amet cursus.',
                    'order' => 5,
                ],
                [
                    'id' => $id . '-content-2-2',
                    'type' => 'text',
                    'title' => 'Implementation Process',
                    'content' => 'Nulla nunc pharetra ut porta facilisi lacus id consequat. Imperdiet consectetur bibendum eget cras eget sit non egestas in. Magna pretium feugiat sodales porttitor ultrices. Viverra in eget nunc imperdiet accumsan.',
                    'order' => 6,
                ],
                [
                    'id' => $id . '-content-3',
                    'type' => 'text',
                    'title' => 'Results and Future Direction',
                    'content' => 'Lorem ipsum dolor sit amet consectetur. Interdum elit eget morbi scelerisque. Facilisis sagittis aliquet nisl pharetra turpis eu et eget et. Placerat aliquam ac amet sit amet egestas proin. Nisl proin quam et hac nibh nibh ut.',
                    'order' => 7,
                ],
            ],

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
                        'https://cdn.simpleicons.org/linkedin',
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

    private function image(
        string $url,
        string $publicId
    ): array {
        return [
            'url' => $url,
            'public_id' => $publicId,
        ];
    }
}