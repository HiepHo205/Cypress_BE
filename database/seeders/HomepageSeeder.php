<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class HomepageSeeder extends Seeder
{
    private int $homepageEntryId = 72;

    public function run(): void
    {
        DB::transaction(function () {
            $entryId = $this->getHomepageEntryId();

            $this->saveMeta($entryId, 'type', 'homepage');

            $this->seedBanner($entryId);
            $this->seedBusinessGrowth($entryId);
            $this->seedIntroduction($entryId);
            $this->seedWhyChooseCypress($entryId);
            $this->seedCaseStudies($entryId);
            $this->seedPricing($entryId);
            $this->seedSuccessStories($entryId);
            $this->seedGeneralInformation($entryId);
            $this->seedNews($entryId);
            $this->seedSideNews($entryId);
            $this->seedStartupNews($entryId);
            $this->seedWorkspaceNews($entryId);
            $this->seedNewsSections($entryId);
            $this->seedLaunchOffer($entryId);
            $this->seedContact($entryId);
        });
    }

    private function getHomepageEntryId(): int
    {
        $exists = DB::table('entries')
            ->where('id', $this->homepageEntryId)
            ->exists();

        if (!$exists) {
            throw new RuntimeException(
                "Homepage entry [{$this->homepageEntryId}] was not found."
            );
        }

        return $this->homepageEntryId;
    }

    private function saveMeta(
        int $entryId,
        string $key,
        mixed $value
    ): void {
        if ($value === null) {
            return;
        }

        if (is_array($value) || is_object($value)) {
            $value = json_encode(
                $value,
                JSON_UNESCAPED_UNICODE |
                    JSON_UNESCAPED_SLASHES
            );
        }

        if (is_bool($value)) {
            $value = $value ? '1' : '0';
        }

        $now = now();

        DB::table('entry_meta')->updateOrInsert(
            [
                'entry_id' => $entryId,
                'meta_key' => $key,
            ],
            [
                'meta_value' => (string) $value,
                'updated_at' => $now,
                'created_at' => $now,
            ]
        );
    }

    private function seedBanner(int $entryId): void
    {
        $this->saveMeta($entryId, 'banner', [
            'image' => [
                'url' => 'https://res.cloudinary.com/droybexbj/image/upload/v1786203687/homepage/banner/qvqttdfw0fypciqbjryw.jpg',
                'public_id' => 'homepage/banner/qvqttdfw0fypciqbjryw',
            ],
            'title' => 'Work from anywhere, anytime.',
            'description' => 'Access over 600 locations worldwide with just one membership card.',
            'primary_button_text' => 'Book a Service',
            'primary_button_url' => '/book-banner',
            'secondary_button_text' => 'See how it works',
            'secondary_button_url' => '/see-banner',
        ]);
    }

    private function seedBusinessGrowth(int $entryId): void
    {
        $this->saveMeta($entryId, 'business_growth', [
            'label' => 'OUR SERVICES',
            'title' => 'Accelerate Your Business Growth',
            'description' => 'We provide everything you need to build, launch, and scale—so you can focus on what matters most: your vision.',
            'packages' => [
                [
                    'id' => 'business-growth-package-1',
                    'number' => 1,
                    'title' => 'IDEA',
                    'packageName' => 'Package 1',
                    'headline' => 'IDEA – Maximum flexibility',
                    'description' => 'Flexible hourly or part-time workspace solutions. The ideal professional touchpoint for independent founders needing high mobility and cost optimization.',
                    'color' => '#2563eb',
                    'active' => true,
                ],
                [
                    'id' => 'business-growth-package-2',
                    'number' => 2,
                    'title' => 'STARTUP',
                    'packageName' => 'Package 2',
                    'headline' => 'STARTUP – Laying a solid foundation',
                    'description' => 'Establish your brand with a prestigious business address. Includes Bonus Marketing Support: Free Fanpage setup and 1 professional post/month to kickstart your presence.',
                    'color' => '#2563eb',
                    'active' => true,
                ],
                [
                    'id' => 'business-growth-package-3',
                    'number' => 3,
                    'title' => 'SCALE UP',
                    'packageName' => 'Package 3',
                    'headline' => 'SCALE UP – Growth Ecosystem (6 USPs)',
                    'description' => 'Full operational takeover. Enjoy our complete ecosystem: Digital Signature, E-tax, Accounting, Web/App Software, and AI Gemini Pro. You focus on growth; we handle the rest.',
                    'color' => '#2563eb',
                    'active' => true,
                ],
                [
                    'id' => 'business-growth-package-4',
                    'number' => 4,
                    'title' => 'IPO',
                    'packageName' => 'Package 4',
                    'headline' => 'IPO – Unique & Breakthrough',
                    'description' => 'Bespoke software and AI workflows tailored to your unique vision. Direct connection to Venture Capital networks and elite mentors to scale your business to the top.',
                    'color' => '#2563eb',
                    'active' => true,
                ],
            ],
        ]);
    }

    private function seedIntroduction(int $entryId): void
    {
        $this->saveMeta($entryId, 'introduction', [
            'image' => [
                'url' => 'https://res.cloudinary.com/droybexbj/image/upload/v1786204219/homepage/introduction/azztbwvizow7tq5gegg1.jpg',
                'public_id' => 'homepage/introduction/azztbwvizow7tq5gegg1',
            ],
            'title' => 'L',
            'description' => "Lorem ipsum dolor sit amet consectetur. Dui leo massa nec sit a vitae vulputate varius. Vehicula tellus diam metus aliquam pretium. Morbi tincidunt mattis ullamcorper ornare vulputate at. Aliquet sed ac pretium fusce.\n\nEget integer commodo varius nisi dolor. Purus aliquet vestibulum faucibus magna pellentesque nisi massa sed. Eleifend accumsan aenean nisl mi vitae laoreet aliquam platea gravida. Vel tristique sed risus vitae tristique quis. Maecenas morbi amet gravida egestas sem est amet. Urna elit turpis enim a gravida. Enim feugiat vulputate porta interdum at.",
            'author' => 'Lê Thành Nhân',
            'position' => '',
            'show_quote_icon' => true,
            'show_author' => true,
        ]);
    }

    private function seedWhyChooseCypress(int $entryId): void
    {
        $this->saveMeta($entryId, 'why_choose_cypress', [
            'badge' => 'Why Choose Cypress Information',
            'title' => 'Why Choose Cypress Information',
            'description' => 'Why Choose Cypress Information',
            'benefits' => [
                [
                    'id' => 'why-benefit-1',
                    'title' => 'Global Network',
                    'description' => 'Reaching over 600 locations in more than 150 cities.',
                    'active' => true,
                    'icon' => [
                        'url' => 'https://res.cloudinary.com/droybexbj/image/upload/v1785839498/homepage/why_choose_cypress/fwrvq4corcvcaws1wwtd.png',
                        'public_id' => 'homepage/why_choose_cypress/fwrvq4corcvcaws1wwtd',
                    ],
                ],
                [
                    'id' => 'why-benefit-2',
                    'title' => 'Flexible terms',
                    'description' => 'Monthly membership or long-term commitment.',
                    'active' => true,
                    'icon' => [
                        'url' => 'https://res.cloudinary.com/droybexbj/image/upload/v1785839582/homepage/why_choose_cypress/i6n7iqick2kf5njjbrhr.png',
                        'public_id' => 'homepage/why_choose_cypress/i6n7iqick2kf5njjbrhr',
                    ],
                ],
                [
                    'id' => 'why-benefit-3',
                    'title' => 'Premium amenities',
                    'description' => 'High-speed Wi-Fi, coffee, and modern meeting rooms.',
                    'active' => true,
                    'icon' => [
                        'url' => 'https://res.cloudinary.com/droybexbj/image/upload/v1785839696/homepage/why_choose_cypress/lgwzryvjy7g120ptopgv.webp',
                        'public_id' => 'homepage/why_choose_cypress/lgwzryvjy7g120ptopgv',
                    ],
                ],
                [
                    'id' => 'why-benefit-4',
                    'title' => 'Safety & Security',
                    'description' => 'Enhanced cleaning and 24/7 security.',
                    'active' => true,
                    'icon' => [
                        'url' => 'https://res.cloudinary.com/droybexbj/image/upload/v1785839739/homepage/why_choose_cypress/nkftpqt4dfrwmaeawe2f.png',
                        'public_id' => 'homepage/why_choose_cypress/nkftpqt4dfrwmaeawe2f',
                    ],
                ],
                [
                    'id' => 'why-benefit-5',
                    'title' => 'Community event',
                    'description' => 'Networking, workshops, and social gatherings.',
                    'active' => true,
                    'icon' => [
                        'url' => 'https://res.cloudinary.com/droybexbj/image/upload/v1785840765/homepage/why_choose_cypress/ckaqo4a0vmeg5ffx73qk.png',
                        'public_id' => 'homepage/why_choose_cypress/ckaqo4a0vmeg5ffx73qk',
                    ],
                ],
                [
                    'id' => 'why-benefit-6',
                    'title' => 'Business support',
                    'description' => 'Handling mail, printing, and hospitality services.',
                    'active' => true,
                    'icon' => [
                        'url' => 'https://res.cloudinary.com/droybexbj/image/upload/v1786331080/homepage/introduction/xvqiphormskizeysibvv.webp',
                        'public_id' => 'homepage/why_choose_cypress/xvqiphormskizeysibvv',
                    ],
                ],
            ],
        ]);
    }

    private function seedCaseStudies(int $entryId): void
    {
        $this->saveMeta($entryId, 'case_studies', [
            'caseStudies' => [
                [
                    'id' => 'case-study-1',
                    'label' => 'DEBUG LABEL:',
                    'title' => 'Case study 1',
                    'subtitle' => null,
                    'summary' => 'Lorem ipsum dolor sit amet consectetur. In dui commodo elit nulla. Pellentesque purus amet gravida ut egestas leo sagittis vulputate. Convallis vitae semper ultrices donec molestie id. Consequat accumsan pulvinar quis etiam at non quis. Elit sed in volutpat facilisis ac. Volutpat donec enim.',
                    'company' => 'CASE STUDY',
                    'planTitle' => 'SERIES A PLAN',
                    'seriesTags' => 'Marketing, Lifestyle, AI Agent, Software',
                    'learnMoreText' => 'Learn more',
                    'learnMoreUrl' => '/learn-more',
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
                    'id' => 'case-study-2',
                    'label' => 'Case Study',
                    'title' => 'Case study 2',
                    'subtitle' => null,
                    'summary' => 'Lorem ipsum dolor sit amet consectetur. In dui commodo elit nulla. Pellentesque purus amet gravida ut egestas leo sagittis vulputate. Convallis vitae semper ultrices donec molestie id. Consequat accumsan pulvinar quis etiam at non quis. Elit sed in volutpat facilisis ac. Volutpat donec enim.',
                    'company' => null,
                    'planTitle' => 'SERIES A PLAN',
                    'seriesTags' => 'Marketing, Lifestyle, AI Agent, Software',
                    'learnMoreText' => 'Learn more',
                    'learnMoreUrl' => '/learn-more',
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
            ],
        ]);
    }

    private function seedPricing(int $entryId): void
    {
        $this->saveMeta($entryId, 'pricing', [
            'title' => 'Transparent Pricing For Every Need',
            'description' => 'From daily passes to enterprise suites, explore our membership options.',
            'button_text' => 'View Pricing',
            'button_link' => '/view-pricing',
        ]);
    }

    private function seedSuccessStories(int $entryId): void
    {
        $this->saveMeta($entryId, 'success_stories', [
            'successStories' => [
                [
                    'id' => 'success-story-1',
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
                    'id' => 'success-story-2',
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
                    'id' => 'success-story-3',
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
                    'id' => 'success-story-4',
                    'name' => 'Wade Warren',
                    'categories' => [
                        'Marketing',
                        'AI Agent',
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
                    'id' => 'success-story-5',
                    'name' => 'Arlene McCoy',
                    'categories' => [
                        'Marketing',
                        'Lifestyle',
                        'AI Agent',
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
                    'id' => 'success-story-6',
                    'name' => 'Guy Hawkins',
                    'categories' => [
                        'Marketing',
                        'Software',
                    ],
                    'role' => 'CEO',
                    'company' => 'Inox Quang Minh',
                    'quote' => 'The network connections alone were worth 10x our membership.',
                    'avatar' => [
                        'url' => 'https://res.cloudinary.com/droybexbj/image/upload/v1786034827/homepage/success-stories/rhbgpmsit98kczn4gqxw.jpg',
                        'public_id' => 'homepage/success-stories/rhbgpmsit98kczn4gqxw',
                    ],
                ],
            ],
        ]);
    }

    private function seedGeneralInformation(int $entryId): void
    {
        $this->saveMeta($entryId, 'general_information', [
            'generalInformations' => [
                [
                    'id' => 'general-information-1',
                    'badge' => 'Create General Information',
                    'title' => 'Create General Information',
                    'description' => "Create General Information\n",
                ],
                [
                    'id' => 'general-information-2',
                    'badge' => 'Create General Information',
                    'title' => 'Create General Information',
                    'description' => 'Create General Information',
                ],
            ],
        ]);
    }

    private function seedNews(int $entryId): void
    {
        $this->saveMeta($entryId, 'news', [
            'label' => 'NEWS',
            'title' => 'News Today',
            'image' => [
                'url' => 'https://res.cloudinary.com/droybexbj/image/upload/v1786011536/homepage/news/fp0auftvkbprg0lavlrl.jpg',
                'public_id' => 'homepage/news/fp0auftvkbprg0lavlrl',
            ],
            'news' => [
                [
                    'id' => 'news-1786126913057-ip1gj0f2',
                    'category' => 'Co-Working Space',
                    'date' => '2026-08-08',
                    'title' => 'GTM Strategies for 2024: How the Best Founders Find Their First 100 Customers',
                    'description' => 'In the automotive industry, stainless steel is not just a common material; it has become a decisive factor in the durability, safety, and aesthetics of modern vehicles. With its corrosion resistance, heat resistance, and lightweight properties, stainless steel is the preferred choice for global car manufacturers. However, not everyone fully understands the difference.',
                    'active' => true,
                    'image' => [
                        'url' => 'https://res.cloudinary.com/droybexbj/image/upload/v1786204889/homepage/news/krgffjz5f4vwpwumvcgp.jpg',
                        'public_id' => 'homepage/news/krgffjz5f4vwpwumvcgp',
                    ],
                ],
            ],
        ]);
    }

    private function seedSideNews(int $entryId): void
    {
        $this->saveMeta($entryId, 'side_news', [
            'sideNews' => [
                [
                    'id' => 'side-news-1',
                    'description' => 'Discover the role of stainless steel in',
                    'image' => [
                        'url' => 'https://res.cloudinary.com/droybexbj/image/upload/v1785924303/homepage/side_news/lx9r00ra2hbkoeok9fl2.jpg',
                        'public_id' => 'homepage/side_news/lx9r00ra2hbkoeok9fl2',
                    ],
                ],
                [
                    'id' => 'side-news-2',
                    'description' => 'Discover the role of stainless steel in',
                    'image' => [
                        'url' => 'https://res.cloudinary.com/droybexbj/image/upload/v1785924319/homepage/side_news/nkzlvn8mwridvpkyowpi.jpg',
                        'public_id' => 'homepage/side_news/nkzlvn8mwridvpkyowpi',
                    ],
                ],
                [
                    'id' => 'side-news-3',
                    'description' => 'Discover the role of stainless steel in',
                    'image' => [
                        'url' => 'https://res.cloudinary.com/droybexbj/image/upload/v1786128424/homepage/side-news/htm7p6gqzz24jzsjkxob.jpg',
                        'public_id' => 'homepage/side-news/htm7p6gqzz24jzsjkxob',
                    ],
                ],
            ],
        ]);
    }

    private function seedStartupNews(int $entryId): void
    {
        $this->saveMeta($entryId, 'startup_news', [
            'startupNews' => [
                [
                    'id' => 'startup-news-1',
                    'title' => 'Lorem ipsum dolor sit amet consect',
                    'active' => true,
                ],
                [
                    'id' => 'startup-news-2',
                    'title' => 'Lorem ipsum dolor sit amet consect',
                    'active' => true,
                ],
                [
                    'id' => 'startup-news-3',
                    'title' => 'Lorem ipsum dolor sit amet consect',
                    'active' => true,
                ],
                [
                    'id' => 'startup-news-4',
                    'title' => 'Lorem ipsum dolor sit amet consect',
                    'active' => true,
                ],
                [
                    'id' => 'startup-news-5',
                    'title' => 'Lorem ipsum dolor sit amet consect',
                    'active' => true,
                ],
            ],
        ]);
    }

    private function seedWorkspaceNews(int $entryId): void
    {
        $this->saveMeta($entryId, 'workspace_news', [
            'workspaceNews' => [
                [
                    'id' => 'workspace-news-1',
                    'title' => 'Lorem ipsum dolor sit amet consect',
                    'active' => true,
                ],
                [
                    'id' => 'workspace-news-2',
                    'title' => 'Lorem ipsum dolor sit amet consect',
                    'active' => true,
                ],
                [
                    'id' => 'workspace-news-3',
                    'title' => 'Lorem ipsum dolor sit amet consect',
                    'active' => true,
                ],
                [
                    'id' => 'workspace-news-4',
                    'title' => 'Lorem ipsum dolor sit amet consect',
                    'active' => true,
                ],
            ],
        ]);
    }

    private function seedNewsSections(int $entryId): void
    {
        $this->saveMeta($entryId, 'news_sections', [
            'sections' => [
                [
                    'id' => 'news-section-1',
                    'key' => 'startup_news',
                    'title' => 'Startup News',
                    'buttonText' => 'View all',
                    'buttonUrl' => '/view-all',
                    'items' => [
                        [
                            'id' => 'startup-section-item-1',
                            'title' => 'Lorem ipsum dolor sit amet consect',
                        ],
                        [
                            'id' => 'startup-section-item-2',
                            'title' => 'Lorem ipsum dolor sit amet consect',
                        ],
                        [
                            'id' => 'startup-section-item-3',
                            'title' => 'Lorem ipsum dolor sit amet consect',
                        ],
                        [
                            'id' => 'startup-section-item-4',
                            'title' => 'Lorem ipsum dolor sit amet consect',
                        ],
                        [
                            'id' => 'startup-section-item-5',
                            'title' => 'Lorem ipsum dolor sit amet consect',
                        ],
                    ],
                ],
                [
                    'id' => 'news-section-2',
                    'key' => 'workspace_trends',
                    'title' => 'Workspace Trends',
                    'buttonText' => '',
                    'buttonUrl' => '',
                    'items' => [
                        [
                            'id' => 'workspace-section-item-1',
                            'title' => 'Lorem ipsum dolor sit amet consect',
                        ],
                        [
                            'id' => 'workspace-section-item-2',
                            'title' => 'Lorem ipsum dolor sit amet consect',
                        ],
                        [
                            'id' => 'workspace-section-item-3',
                            'title' => 'Lorem ipsum dolor sit amet consect',
                        ],
                        [
                            'id' => 'workspace-section-item-4',
                            'title' => 'Lorem ipsum dolor sit amet consect',
                        ],
                    ],
                ],
            ],
        ]);
    }

    private function seedLaunchOffer(int $entryId): void
    {
        $this->saveMeta($entryId, 'launch_offer', [
            'title' => 'Limited-Time Launch Offer',
            'subtitle' => 'Join Cypress Hub before May 30th and save up to 50% on membership fees.',
            'buttonText' => 'Get the offer now!',
            'buttonUrl' => '/get-offer',
            'expiryDate' => '2026-05-30',
            'seats' => 'A total of 70 seats',
        ]);
    }

    private function seedContact(int $entryId): void
    {
        $this->saveMeta($entryId, 'contact', [
            'image' => [
                'url' => 'https://res.cloudinary.com/droybexbj/image/upload/v1786205297/homepage/contact/nrhc9skgqnikqcroknjn.jpg',
                'public_id' => 'homepage/contact/nrhc9skgqnikqcroknjn',
            ],

            'title' => 'Take a tour of our headquarters.',
            'description' => 'Experience the difference at Cypress. Book your tour today.',
            'termsText' => 'By clicking the button below, you agree to our',
            'termsLabel' => 'Terms of Service',
            'termsUrl' => '/terms-of-service',
            'buttonText' => 'Submit',
            'buttonUrl' => '/submit',
            'labels' => [
                [
                    'id' => 'contact-label-1',
                    'title' => 'Name *',
                    'placeholder' => 'Enter your full name',
                    'type' => 'input',
                    'options' => [],
                ],
                [
                    'id' => 'contact-label-2',
                    'title' => 'Email *',
                    'placeholder' => 'Enter your email',
                    'type' => 'input',
                    'options' => [],
                ],
                [
                    'id' => 'contact-label-3',
                    'title' => 'Company name',
                    'placeholder' => 'Enter your company name',
                    'type' => 'input',
                    'options' => [],
                ],
                [
                    'id' => 'contact-label-4',
                    'title' => 'Phone *',
                    'placeholder' => '+84',
                    'type' => 'select',
                    'options' => [
                        [
                            'id' => 'contact-option-phone-1',
                            'value' => '+84',
                        ],
                        [
                            'id' => 'contact-option-phone-2',
                            'value' => '+83',
                        ],
                    ],
                ],
                [
                    'id' => 'contact-label-5',
                    'title' => 'A service of interest',
                    'placeholder' => 'Choose a service that interests you',
                    'type' => 'select',
                    'options' => [
                        [
                            'id' => 'contact-option-service-1',
                            'value' => 'Choose a service that interests you',
                        ],
                    ],
                ],
                [
                    'id' => 'contact-label-6',
                    'title' => 'Message',
                    'placeholder' => 'Tell us more about your needs',
                    'type' => 'textarea',
                    'options' => [],
                ],
            ],
        ]);
    }
}
