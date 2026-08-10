<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class HomepageSeeder extends Seeder
{
    private int $createdBy = 6;

    public function run(): void
    {
        DB::transaction(function () {
            $this->seedBanner();
            $this->seedBusinessGrowth();
            $this->seedIntroduction();
            $this->seedWhyChooseCypress();
            $this->seedCaseStudies();
            $this->seedPricing();
            $this->seedSuccessStories();
            $this->seedGeneralInformation();
            $this->seedNews();
            $this->seedSideNews();
            $this->seedLaunchOffer();
            $this->seedNewsSections();
            $this->seedContact();
        });
    }

    private function collectionId(string $collectionName): int
    {
        $id = DB::table('collections')
            ->where('collection_name', $collectionName)
            ->value('id');

        if (!$id) {
            throw new RuntimeException(
                "Collection [{$collectionName}] was not found."
            );
        }

        return (int) $id;
    }

    private function createEntry(int $collectionId): int
    {
        return DB::table('entries')->insertGetId([
            'collection_id' => $collectionId,
            'status' => 'published',
            'created_by' => $this->createdBy,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function addMeta(
        int $entryId,
        string $key,
        mixed $value
    ): void {
        if (is_array($value)) {
            $value = json_encode(
                $value,
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            );
        }

        if (is_bool($value)) {
            $value = $value ? '1' : '0';
        }

        if ($value === null) {
            return;
        }

        DB::table('entry_meta')->insert([
            'entry_id' => $entryId,
            'meta_key' => $key,
            'meta_value' => (string) $value,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function addRelation(
        int $parentId,
        int $childId,
        string $relationType
    ): void {
        DB::table('entry_relations')->insert([
            'parent_entry_id' => $parentId,
            'child_entry_id' => $childId,
            'relation_type' => $relationType,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function addImageMeta(
        int $entryId,
        string $prefix,
        ?array $image
    ): void {
        if (!$image) {
            return;
        }

        $this->addMeta(
            $entryId,
            "{$prefix}_url",
            $image['url'] ?? null
        );

        $this->addMeta(
            $entryId,
            "{$prefix}_public_id",
            $image['public_id'] ?? null
        );
    }

    private function seedBanner(): void
    {
        $collectionId = $this->collectionId('homepage_banner');

        $bannerId = $this->createEntry($collectionId);

        $this->addMeta(
            $bannerId,
            'title',
            'Work from anywhere, anytime.'
        );

        $this->addMeta(
            $bannerId,
            'description',
            'Access over 600 locations worldwide with just one membership card.'
        );

        $this->addMeta(
            $bannerId,
            'primary_button_text',
            'Book a Service'
        );

        $this->addMeta(
            $bannerId,
            'primary_button_url',
            '/book-banner'
        );

        $this->addMeta(
            $bannerId,
            'secondary_button_text',
            'See how it works'
        );

        $this->addMeta(
            $bannerId,
            'secondary_button_url',
            '/see-banner'
        );

        $this->addImageMeta($bannerId, 'image', [
            'url' => 'https://res.cloudinary.com/droybexbj/image/upload/v1786203687/homepage/banner/qvqttdfw0fypciqbjryw.jpg',
            'public_id' => 'homepage/banner/qvqttdfw0fypciqbjryw',
        ]);
    }

    private function seedBusinessGrowth(): void
    {
        $collectionId = $this->collectionId(
            'homepage_business_growth'
        );

        $packageCollectionId = $this->collectionId(
            'homepage_business_growth_package'
        );

        $sectionId = $this->createEntry($collectionId);

        $this->addMeta(
            $sectionId,
            'label',
            'OUR SERVICES'
        );

        $this->addMeta(
            $sectionId,
            'title',
            'Accelerate Your Business Growth'
        );

        $this->addMeta(
            $sectionId,
            'description',
            'We provide everything you need to build, launch, and scale—so you can focus on what matters most: your vision.'
        );

        $packages = [
            [
                'number' => '1',
                'title' => 'IDEA',
                'package_name' => 'Package 1',
                'headline' => 'IDEA – Maximum flexibility',
                'description' => 'Flexible hourly or part-time workspace solutions. The ideal professional touchpoint for independent founders needing high mobility and cost optimization.',
                'color' => '#2563eb',
                'active' => true,
            ],
            [
                'number' => '2',
                'title' => 'STARTUP',
                'package_name' => 'Package 2',
                'headline' => 'STARTUP – Laying a solid foundation',
                'description' => 'Establish your brand with a prestigious business address. Includes Bonus Marketing Support: Free Fanpage setup and 1 professional post/month to kickstart your presence.',
                'color' => '#2563eb',
                'active' => true,
            ],
            [
                'number' => '3',
                'title' => 'SCALE UP',
                'package_name' => 'Package 3',
                'headline' => 'SCALE UP – Growth Ecosystem (6 USPs)',
                'description' => 'Full operational takeover. Enjoy our complete ecosystem: Digital Signature, E-tax, Accounting, Web/App Software, and AI Gemini Pro. You focus on growth; we handle the rest.',
                'color' => '#2563eb',
                'active' => true,
            ],
            [
                'number' => '4',
                'title' => 'IPO',
                'package_name' => 'Package 4',
                'headline' => 'IPO – Unique & Breakthrough',
                'description' => 'Bespoke software and AI workflows tailored to your unique vision. Direct connection to Venture Capital networks and elite mentors to scale your business to the top.',
                'color' => '#2563eb',
                'active' => true,
            ],
        ];

        foreach ($packages as $package) {
            $packageId = $this->createEntry($packageCollectionId);

            foreach ($package as $key => $value) {
                $this->addMeta(
                    $packageId,
                    $key,
                    $value
                );
            }

            $this->addRelation(
                $sectionId,
                $packageId,
                'business_growth_package'
            );
        }
    }

    private function seedIntroduction(): void
    {
        $collectionId = $this->collectionId(
            'homepage_introduction'
        );

        $entryId = $this->createEntry($collectionId);

        $this->addMeta(
            $entryId,
            'title',
            'L'
        );

        $this->addMeta(
            $entryId,
            'description',
            "Lorem ipsum dolor sit amet consectetur. Dui leo massa nec sit a vitae vulputate varius. Vehicula tellus diam metus aliquam pretium. Morbi tincidunt mattis ullamcorper ornare vulputate at. Aliquet sed ac pretium fusce .\n\nEget integer commodo varius nisi dolor. Purus aliquet vestibulum faucibus magna pellentesque nisi massa sed. Eleifend accumsan aenean nisl mi vitae laoreet aliquam platea gravida. Vel tristique sed risus vitae tristique quis. Maecenas morbi amet gravida egestas sem est amet. Urna elit turpis enim a gravida. Enim feugiat vulputate porta interdum at."
        );

        $this->addMeta(
            $entryId,
            'author',
            'Lê Thành Nhân'
        );

        $this->addMeta(
            $entryId,
            'position',
            "''"
        );

        $this->addMeta(
            $entryId,
            'show_quote_icon',
            true
        );

        $this->addMeta(
            $entryId,
            'show_author',
            true
        );

        $this->addImageMeta($entryId, 'image', [
            'url' => 'https://res.cloudinary.com/droybexbj/image/upload/v1786204219/homepage/introduction/azztbwvizow7tq5gegg1.jpg',
            'public_id' => 'homepage/introduction/azztbwvizow7tq5gegg1',
        ]);
    }

    private function seedWhyChooseCypress(): void
    {
        $collectionId = $this->collectionId(
            'homepage_why_choose_cypress'
        );

        $benefitCollectionId = $this->collectionId(
            'homepage_why_choose_cypress_benefit'
        );

        $sectionId = $this->createEntry($collectionId);

        $this->addMeta(
            $sectionId,
            'badge',
            'Why Choose Cypress InformationHomepage Banner'
        );

        $this->addMeta(
            $sectionId,
            'title',
            'Why Choose Cypress Information'
        );

        $this->addMeta(
            $sectionId,
            'description',
            'Why Choose Cypress Information'
        );

        $benefits = [
            [
                'title' => 'Global Network',
                'description' => 'Reaching over 600 locations in more than 150 cities.',
                'active' => true,
                'icon' => [
                    'url' => 'https://res.cloudinary.com/droybexbj/image/upload/v1785839498/homepage/why_choose_cypress/fwrvq4corcvcaws1wwtd.png',
                    'public_id' => 'homepage/why_choose_cypress/fwrvq4corcvcaws1wwtd',
                ],
            ],
            [
                'title' => 'Flexible terms',
                'description' => 'Monthly membership or long-term commitment.',
                'active' => true,
                'icon' => [
                    'url' => 'https://res.cloudinary.com/droybexbj/image/upload/v1785839582/homepage/why_choose_cypress/i6n7iqick2kf5njjbrhr.png',
                    'public_id' => 'homepage/why_choose_cypress/i6n7iqick2kf5njjbrhr',
                ],
            ],
            [
                'title' => 'Premium amenities',
                'description' => 'High-speed Wi-Fi, coffee, and modern meeting rooms.',
                'active' => true,
                'icon' => [
                    'url' => 'https://res.cloudinary.com/droybexbj/image/upload/v1785839696/homepage/why_choose_cypress/lgwzryvjy7g120ptopgv.webp',
                    'public_id' => 'homepage/why_choose_cypress/lgwzryvjy7g120ptopgv',
                ],
            ],
            [
                'title' => 'Safety & Security',
                'description' => 'Enhanced cleaning and 24/7 security.',
                'active' => true,
                'icon' => [
                    'url' => 'https://res.cloudinary.com/droybexbj/image/upload/v1785839739/homepage/why_choose_cypress/nkftpqt4dfrwmaeawe2f.png',
                    'public_id' => 'homepage/why_choose_cypress/nkftpqt4dfrwmaeawe2f',
                ],
            ],
            [
                'title' => 'Community event',
                'description' => 'Networking, workshops, and social gatherings.',
                'active' => true,
                'icon' => [
                    'url' => 'https://res.cloudinary.com/droybexbj/image/upload/v1785840765/homepage/why_choose_cypress/ckaqo4a0vmeg5ffx73qk.png',
                    'public_id' => 'homepage/why_choose_cypress/ckaqo4a0vmeg5ffx73qk',
                ],
            ],
            [
                'title' => 'Business support',
                'description' => 'Handling mail, printing, and hospitality services.',
                'active' => true,
                'icon' => [
                    'url' => 'https://res.cloudinary.com/droybexbj/image/upload/v1786331080/homepage/introduction/xvqiphormskizeysibvv.webp',
                    'public_id' => 'homepage/why_choose_cypress/xvqiphormskizeysibvv',
                ],
            ],
        ];

        foreach ($benefits as $benefit) {
            $benefitId = $this->createEntry(
                $benefitCollectionId
            );

            $this->addMeta(
                $benefitId,
                'title',
                $benefit['title']
            );

            $this->addMeta(
                $benefitId,
                'description',
                $benefit['description']
            );

            $this->addMeta(
                $benefitId,
                'active',
                $benefit['active']
            );

            $this->addImageMeta(
                $benefitId,
                'icon',
                $benefit['icon']
            );

            $this->addRelation(
                $sectionId,
                $benefitId,
                'why_choose_cypress_benefit'
            );
        }
    }

    private function seedCaseStudies(): void
    {
        $collectionId = $this->collectionId(
            'homepage_case_study'
        );

        $caseStudies = [
            [
                'label' => 'DEBUG LABEL:',
                'title' => 'Case study 1',
                'subtitle' => null,
                'summary' => 'Lorem ipsum dolor sit amet consectetur. In dui commodo elit nulla. Pellentesque purus amet gravida ut egestasleo sagittis vulputate. Convallis vitae in lectus semper ultrices donec enim molestie id. Consequat accumsan pulvinar quis etiam at non quis. Elit sed in volutpat facilisis ac. Volutpat donec enim',
                'company' => 'CASE STUDY',
                'plan_title' => 'SERIES A PLAN',
                'series_tags' => 'Marketing, Lifestyle, AI Agent, Software',
                'learn_more_text' => 'Learn more',
                'learn_more_url' => '/learn-more',
                'active' => true,
                'logo' => [
                    'url' => 'https://res.cloudinary.com/droybexbj/image/upload/v1785834513/homepage/case_studies/logo/g30a5kuk7eckqbcvzn7s.jpg',
                    'public_id' => 'homepage/case_studies/logo/g30a5kuk7eckqbcvzn7s',
                ],
                'image' => [
                    'url' => 'https://res.cloudinary.com/droybexbj/image/upload/v1785832923/homepage/case_studies/delf3mp2jkbyrdz9lyxr.jpg',
                    'public_id' => 'homepage/case_studies/delf3mp2jkbyrdz9lyxr',
                ],
            ],
            [
                'label' => 'Case Study ',
                'title' => 'Case study 1',
                'subtitle' => null,
                'summary' => 'Lorem ipsum dolor sit amet consectetur. In dui commodo elit nulla. Pellentesque purus amet gravida ut egestas leo sagittis vulputate. Convallis vitae semper ultrices donec molestie id. Consequat accumsan pulvinar quis etiam at non quis. Elit sed in volutpat facilisis ac. Volutpat donec enim.',
                'company' => null,
                'plan_title' => 'SERIES A PLAN',
                'series_tags' => 'Marketing, Lifestyle, AI Agent, Software',
                'learn_more_text' => 'Learn more',
                'learn_more_url' => '/learn-more',
                'active' => true,
                'logo' => [
                    'url' => 'https://res.cloudinary.com/droybexbj/image/upload/v1785834781/homepage/case_studies/logo/rtsim73k5bphlhazxas8.jpg',
                    'public_id' => 'homepage/case_studies/logo/rtsim73k5bphlhazxas8',
                ],
                'image' => [
                    'url' => 'https://res.cloudinary.com/droybexbj/image/upload/v1785834782/homepage/case_studies/image/uujhfngc8wavvwpnkglt.jpg',
                    'public_id' => 'homepage/case_studies/image/uujhfngc8wavvwpnkglt',
                ],
            ],
        ];

        foreach ($caseStudies as $caseStudy) {
            $entryId = $this->createEntry($collectionId);

            foreach (
                [
                    'label',
                    'title',
                    'subtitle',
                    'summary',
                    'company',
                    'plan_title',
                    'series_tags',
                    'learn_more_text',
                    'learn_more_url',
                    'active',
                ] as $key
            ) {
                $this->addMeta(
                    $entryId,
                    $key,
                    $caseStudy[$key]
                );
            }

            $this->addImageMeta(
                $entryId,
                'logo',
                $caseStudy['logo']
            );

            $this->addImageMeta(
                $entryId,
                'image',
                $caseStudy['image']
            );
        }
    }

    private function seedPricing(): void
    {
        $collectionId = $this->collectionId(
            'homepage_pricing'
        );

        $entryId = $this->createEntry($collectionId);

        $this->addMeta(
            $entryId,
            'title',
            'Transparent Pricing For Every Need'
        );

        $this->addMeta(
            $entryId,
            'description',
            'From daily passes to enterprise suites, explore our membership options.'
        );

        $this->addMeta(
            $entryId,
            'button_text',
            'View Pricing'
        );

        $this->addMeta(
            $entryId,
            'button_link',
            '/view-pricing'
        );
    }


    private function seedSuccessStories(): void
    {
        $collectionId = $this->collectionId(
            'homepage_success_story'
        );

        $stories = [
            [
                'name' => 'Courtney Henry',
                'categories' => [
                    'Marketing',
                    'Lifestyle',
                    'AI Agent',
                ],
                'role' => 'CEO',
                'company' => 'Inox Quang Minh',
                'quote' => 'The network connections alone were worth 10x our membership.',
                'avatar' => [
                    'url' => 'https://res.cloudinary.com/droybexbj/image/upload/v1786034827/homepage/success-stories/rhbgpmsit98kczn4gqxw.jpg',
                    'public_id' => 'homepage/success-stories/rhbgpmsit98kczn4gqxw',
                ],
            ],
            [
                'name' => 'Marvin McKinney',
                'categories' => [
                    'Marketing',
                    'Lifestyle',
                ],
                'role' => 'CEO',
                'company' => 'Inox Quang Minh',
                'quote' => 'The network connections alone were worth 10x our membership.',
                'avatar' => [
                    'url' => 'https://res.cloudinary.com/droybexbj/image/upload/v1786034866/homepage/success-stories/hire9vjxfuhdnfqvlny0.jpg',
                    'public_id' => 'homepage/success-stories/hire9vjxfuhdnfqvlny0',
                ],
            ],
            [
                'name' => 'Arlene McCoy',
                'categories' => [
                    'Marketing',
                    'AI Agent',
                    'Lifestyle',
                ],
                'role' => 'CEO',
                'company' => 'Inox Quang Minh',
                'quote' => 'The network connections alone were worth 10x our membership.',
                'avatar' => [
                    'url' => 'https://res.cloudinary.com/droybexbj/image/upload/v1786035470/homepage/success-stories/jsmma3rklvhnrskezglz.jpg',
                    'public_id' => 'homepage/success-stories/jsmma3rklvhnrskezglz',
                ],
            ],
            [
                'name' => 'Wade Warren',
                'categories' => [
                    'Marketing',
                    'AI Agent',
                    'Lifestyle',
                ],
                'role' => 'CEO',
                'company' => 'Inox Quang Minh',
                'quote' => 'The network connections alone were worth 10x our membership.',
                'avatar' => null,
            ],
            [
                'name' => 'Arlene McCoy',
                'categories' => [
                    'Marketing',
                    'Lifestyle',
                    'AI Agent',
                ],
                'role' => 'CEO',
                'company' => 'Inox Quang Minh',
                'quote' => 'The network connections alone were worth 10x our membership.',
                'avatar' => null,
            ],
            [
                'name' => 'Guy Hawkins',
                'categories' => [
                    'Marketing',
                    'Software',
                ],
                'role' => 'CEO',
                'company' => 'Inox Quang Minh',
                'quote' => 'The network connections alone were worth 10x our membership.',
                'avatar' => null,
            ],
        ];

        foreach ($stories as $story) {
            $entryId = $this->createEntry($collectionId);

            $this->addMeta(
                $entryId,
                'name',
                $story['name']
            );

            $this->addMeta(
                $entryId,
                'categories',
                $story['categories']
            );

            $this->addMeta(
                $entryId,
                'role',
                $story['role']
            );

            $this->addMeta(
                $entryId,
                'company',
                $story['company']
            );

            $this->addMeta(
                $entryId,
                'quote',
                $story['quote']
            );

            $this->addImageMeta(
                $entryId,
                'avatar',
                $story['avatar']
            );
        }
    }


    private function seedGeneralInformation(): void
    {
        $collectionId = $this->collectionId(
            'homepage_general_information'
        );

        $items = [
            [
                'badge' => 'Create General Information',
                'title' => 'Create General Information',
                'description' => "Create General Information\n",
            ],
            [
                'badge' => 'Create General Information',
                'title' => 'Create General Information',
                'description' => 'Create General Information',
            ],
        ];

        foreach ($items as $item) {
            $entryId = $this->createEntry($collectionId);

            foreach ($item as $key => $value) {
                $this->addMeta(
                    $entryId,
                    $key,
                    $value
                );
            }
        }
    }

    private function seedNews(): void
    {
        $collectionId = $this->collectionId(
            'homepage_news'
        );

        $sectionId = $this->createEntry($collectionId);

        $this->addMeta(
            $sectionId,
            'label',
            'NEWS'
        );

        $this->addMeta(
            $sectionId,
            'title',
            'News Today'
        );

        $newsCollectionId = $this->collectionId(
            'homepage_news_item'
        );

        $news = [
            [
                'category' => 'Co-Working Space',
                'date' => '2026-08-08',
                'title' => 'GTM Strategies for 2024: How the Best Founders Find Their First 100 Customers',
                'description' => 'In the automotive industry, stainless steel is not just a common material; it has become a decisive factor in the durability, safety, and aesthetics of modern vehicles. With its corrosion resistance, heat resistance, and lightweight properties, stainless steel is the preferred choice for global car manufacturers. However, not everyone fully understands the differe',
                'active' => null,
                'image' => [
                    'url' => 'https://res.cloudinary.com/droybexbj/image/upload/v1786204889/homepage/news/krgffjz5f4vwpwumvcgp.jpg',
                    'public_id' => 'homepage/news/krgffjz5f4vwpwumvcgp',
                ],
            ],
        ];

        foreach ($news as $item) {
            $newsId = $this->createEntry($newsCollectionId);

            foreach (
                [
                    'category',
                    'date',
                    'title',
                    'description',
                    'active',
                ] as $key
            ) {
                $this->addMeta(
                    $newsId,
                    $key,
                    $item[$key]
                );
            }

            $this->addImageMeta(
                $newsId,
                'image',
                $item['image']
            );

            $this->addRelation(
                $sectionId,
                $newsId,
                'homepage_news_item'
            );
        }
    }

    private function seedSideNews(): void
    {
        $collectionId = $this->collectionId(
            'homepage_side_news'
        );

        $items = [
            [
                'description' => 'Discover the role of stainless steel in',
                'image' => [
                    'url' => 'https://res.cloudinary.com/droybexbj/image/upload/v1785924303/homepage/side_news/lx9r00ra2hbkoeok9fl2.jpg',
                    'public_id' => 'homepage/side_news/lx9r00ra2hbkoeok9fl2',
                ],
            ],
            [
                'description' => 'Discover the role of stainless steel in',
                'image' => [
                    'url' => 'https://res.cloudinary.com/droybexbj/image/upload/v1785924319/homepage/side_news/nkzlvn8mwridvpkyowpi.jpg',
                    'public_id' => 'homepage/side_news/nkzlvn8mwridvpkyowpi',
                ],
            ],
            [
                'description' => 'Discover the role of stainless steel in',
                'image' => [
                    'url' => 'https://res.cloudinary.com/droybexbj/image/upload/v1786128424/homepage/side-news/htm7p6gqzz24jzsjkxob.jpg',
                    'public_id' => 'homepage/side-news/htm7p6gqzz24jzsjkxob',
                ],
            ],
        ];

        foreach ($items as $item) {
            $entryId = $this->createEntry($collectionId);

            $this->addMeta(
                $entryId,
                'description',
                $item['description']
            );

            $this->addImageMeta(
                $entryId,
                'image',
                $item['image']
            );
        }
    }
    private function seedLaunchOffer(): void
    {
        $collectionId = $this->collectionId(
            'homepage_launch_offer'
        );

        $entryId = $this->createEntry($collectionId);

        $this->addMeta(
            $entryId,
            'title',
            'Limited-Time Launch Offer'
        );

        $this->addMeta(
            $entryId,
            'subtitle',
            "Join Cypress Hub before May 30th and save up to 50% on membership fees.\n"
        );

        $this->addMeta(
            $entryId,
            'button_text',
            'Get the offer now!'
        );

        $this->addMeta(
            $entryId,
            'button_url',
            '/get-offer'
        );

        $this->addMeta(
            $entryId,
            'expiry_date',
            '2026-05-30'
        );

        $this->addMeta(
            $entryId,
            'seats',
            'A total of 70 seats'
        );
    }
    private function seedNewsSections(): void
    {
        $sectionCollectionId = $this->collectionId(
            'homepage_news_section'
        );

        $itemCollectionId = $this->collectionId(
            'homepage_news_section_item'
        );

        $sections = [
            [
                'key' => '',
                'title' => 'Startup News',
                'button_text' => 'View all',
                'button_url' => '/view-all',
                'items' => [
                    'Lorem ipsum dolor sit amet consect',
                    'Lorem ipsum dolor sit amet consect',
                    'Lorem ipsum dolor sit amet consect',
                    'Lorem ipsum dolor sit amet consect',
                    'Lorem ipsum dolor sit amet consect',
                ],
            ],
            [
                'key' => '',
                'title' => 'Workspace Trends',
                'button_text' => '',
                'button_url' => '',
                'items' => [
                    'Lorem ipsum dolor sit amet consect',
                    'Lorem ipsum dolor sit amet consect',
                    'Lorem ipsum dolor sit amet consect',
                    'Lorem ipsum dolor sit amet consect',
                ],
            ],
        ];

        foreach ($sections as $section) {
            $sectionId = $this->createEntry(
                $sectionCollectionId
            );

            $this->addMeta(
                $sectionId,
                'key',
                $section['key']
            );

            $this->addMeta(
                $sectionId,
                'title',
                $section['title']
            );

            $this->addMeta(
                $sectionId,
                'button_text',
                $section['button_text']
            );

            $this->addMeta(
                $sectionId,
                'button_url',
                $section['button_url']
            );

            foreach ($section['items'] as $title) {
                $itemId = $this->createEntry(
                    $itemCollectionId
                );

                $this->addMeta(
                    $itemId,
                    'title',
                    $title
                );

                $this->addRelation(
                    $sectionId,
                    $itemId,
                    'news_section_item'
                );
            }
        }
    }
    private function seedContact(): void
    {
        $collectionId = $this->collectionId(
            'homepage_contact'
        );

        $labelCollectionId = $this->collectionId(
            'homepage_contact_label'
        );

        $optionCollectionId = $this->collectionId(
            'homepage_contact_option'
        );

        $contactId = $this->createEntry($collectionId);

        $this->addMeta(
            $contactId,
            'title',
            'Take a tour of our headquarters.'
        );

        $this->addMeta(
            $contactId,
            'description',
            'Experience the difference at Cypress. Book your tour today.'
        );

        $this->addImageMeta($contactId, 'image', [
            'url' => 'https://res.cloudinary.com/droybexbj/image/upload/v1786205297/homepage/contact/nrhc9skgqnikqcroknjn.jpg',
            'public_id' => 'homepage/contact/nrhc9skgqnikqcroknjn',
        ]);

        $this->addMeta(
            $contactId,
            'terms_text',
            'By clicking the button below, you agree to our'
        );

        $this->addMeta(
            $contactId,
            'terms_label',
            'Submit'
        );

        $this->addMeta(
            $contactId,
            'terms_url',
            '/submit'
        );

        $this->addMeta(
            $contactId,
            'button_text',
            'Terms of Service'
        );

        $this->addMeta(
            $contactId,
            'button_url',
            '/term-of-service'
        );

        $labels = [
            [
                'title' => 'Name *',
                'placeholder' => 'Enter your full name',
                'type' => 'input',
                'options' => [
                    'thanh',
                    'Contact Management',
                    'Button text',
                ],
            ],
            [
                'title' => 'Email *',
                'placeholder' => 'Enter your email',
                'type' => 'input',
                'options' => [],
            ],
            [
                'title' => 'Company name',
                'placeholder' => 'Enter your company name',
                'type' => 'input',
                'options' => [],
            ],
            [
                'title' => 'Phone *',
                'placeholder' => '+84',
                'type' => 'select',
                'options' => [
                    '+84',
                    '+83',
                ],
            ],
            [
                'title' => 'A service of interest',
                'placeholder' => 'Choose a service that interests you',
                'type' => 'select',
                'options' => [
                    'Choose a service that interests you',
                ],
            ],
            [
                'title' => 'Message',
                'placeholder' => 'Tell us more about your needs',
                'type' => 'textarea',
                'options' => [],
            ],
        ];

        foreach ($labels as $label) {
            $labelId = $this->createEntry(
                $labelCollectionId
            );

            $this->addMeta(
                $labelId,
                'title',
                $label['title']
            );

            $this->addMeta(
                $labelId,
                'placeholder',
                $label['placeholder']
            );

            $this->addMeta(
                $labelId,
                'type',
                $label['type']
            );

            $this->addRelation(
                $contactId,
                $labelId,
                'contact_label'
            );

            foreach ($label['options'] as $value) {
                $optionId = $this->createEntry(
                    $optionCollectionId
                );

                $this->addMeta(
                    $optionId,
                    'value',
                    $value
                );

                $this->addRelation(
                    $labelId,
                    $optionId,
                    'contact_label_option'
                );
            }
        }
    }
}
