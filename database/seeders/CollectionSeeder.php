<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CollectionSeeder extends Seeder
{
    public function run(): void
    {
        $collections = [
            [
                'collection_name' => 'companies',
                'api_endpoint' => 'companies',
                'is_system_type' => false,
            ],
            [
                'collection_name' => 'plans',
                'api_endpoint' => 'plans',
                'is_system_type' => false,
            ],
            [
                'collection_name' => 'subscriptions',
                'api_endpoint' => 'subscriptions',
                'is_system_type' => false,
            ],
            [
                'collection_name' => 'features',
                'api_endpoint' => 'features',
                'is_system_type' => false,
            ],
            [
                'collection_name' => 'benefits',
                'api_endpoint' => 'benefits',
                'is_system_type' => false,
            ],
            [
                'collection_name' => 'service_requests',
                'api_endpoint' => 'service_requests',
                'is_system_type' => false,
            ],
            [
                'collection_name' => 'package_requests',
                'api_endpoint' => 'package_requests',
                'is_system_type' => false,
            ],
            [
                'collection_name' => 'user_package',
                'api_endpoint' => 'user_package',
                'is_system_type' => false,
            ],
            [
                'collection_name' => 'homepage_banner',
                'api_endpoint' => 'homepage_banner',
                'is_system_type' => false,
            ],
            [
                'collection_name' => 'homepage_business_growth',
                'api_endpoint' => 'homepage_business_growth',
                'is_system_type' => false,
            ],
            [
                'collection_name' => 'homepage_business_growth_package',
                'api_endpoint' => 'homepage_business_growth_package',
                'is_system_type' => false,
            ],
            [
                'collection_name' => 'homepage_introduction',
                'api_endpoint' => 'homepage_introduction',
                'is_system_type' => false,
            ],
            [
                'collection_name' => 'homepage_why_choose_cypress',
                'api_endpoint' => 'homepage_why_choose_cypress',
                'is_system_type' => false,
            ],
            [
                'collection_name' => 'homepage_why_choose_cypress_benefit',
                'api_endpoint' => 'homepage_why_choose_cypress_benefit',
                'is_system_type' => false,
            ],
            [
                'collection_name' => 'homepage_case_study',
                'api_endpoint' => 'homepage_case_study',
                'is_system_type' => false,
            ],
            [
                'collection_name' => 'homepage_pricing',
                'api_endpoint' => 'homepage_pricing',
                'is_system_type' => false,
            ],
            [
                'collection_name' => 'homepage_success_story',
                'api_endpoint' => 'homepage_success_story',
                'is_system_type' => false,
            ],
            [
                'collection_name' => 'homepage_general_information',
                'api_endpoint' => 'homepage_general_information',
                'is_system_type' => false,
            ],
            [
                'collection_name' => 'homepage_news',
                'api_endpoint' => 'homepage_news',
                'is_system_type' => false,
            ],
            [
                'collection_name' => 'homepage_news_item',
                'api_endpoint' => 'homepage_news_item',
                'is_system_type' => false,
            ],
            [
                'collection_name' => 'homepage_side_news',
                'api_endpoint' => 'homepage_side_news',
                'is_system_type' => false,
            ],
            [
                'collection_name' => 'homepage_launch_offer',
                'api_endpoint' => 'homepage_launch_offer',
                'is_system_type' => false,
            ],
            [
                'collection_name' => 'homepage_news_section',
                'api_endpoint' => 'homepage_news_section',
                'is_system_type' => false,
            ],
            [
                'collection_name' => 'homepage_news_section_item',
                'api_endpoint' => 'homepage_news_section_item',
                'is_system_type' => false,
            ],
            [
                'collection_name' => 'homepage_contact',
                'api_endpoint' => 'homepage_contact',
                'is_system_type' => false,
            ],
            [
                'collection_name' => 'homepage_contact_label',
                'api_endpoint' => 'homepage_contact_label',
                'is_system_type' => false,
            ],
            [
                'collection_name' => 'homepage_contact_option',
                'api_endpoint' => 'homepage_contact_option',
                'is_system_type' => false,
            ],
            [
                'collection_name' => 'locations',
                'api_endpoint' => 'locations',
                'is_system_type' => false,
            ],
        ];

        foreach ($collections as $collection) {
            DB::table('collections')->updateOrInsert(
                ['api_endpoint' => $collection['api_endpoint']],
                [
                    'collection_name' => $collection['collection_name'],
                    'is_system_type' => $collection['is_system_type'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            );
        }
    }
}
