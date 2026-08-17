<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BenefitSeeder extends Seeder
{
    public function run(): void
    {
        $createdBy = DB::table('users')->orderBy('id')->value('id');

        if (!$createdBy) {
            throw new \RuntimeException('No user found for benefit seeding.');
        }

        $benefitCollectionId = DB::table('collections')
            ->where('collection_name', 'benefits')
            ->value('id');

        $plans = [
            'Startup' => [
                'Workspace' => ['Fixed Seating'],

                'Meeting Room' => ['10 hours/month', '20% discount'],

                'Printing Service' => ['20 pages/month'],

                'Screen Rental' => ['20% discount'],

                'Marketing Services' => ['Included'],
            ],

            'Scale Up' => [
                'Workspace' => ['Flexible Seating'],

                'Meeting Room' => ['20 hours/month', '50% discount'],

                'Printing Service' => ['50 pages/month'],

                'Screen Rental' => ['50% discount'],

                'Marketing Services' => ['Included'],

                'Operations Consulting' => ['Included'],

                'Software Consulting' => ['Included'],

                'AI Agent Consulting' => ['Included'],
            ],

            'IPO' => [
                'Workspace' => ['Dedicated Office'],

                'Meeting Room' => ['Unlimited'],

                'Printing Service' => ['Unlimited'],

                'Screen Rental' => ['Free'],

                'Marketing Services' => ['Included'],

                'Operations Consulting' => ['Included'],

                'Software Consulting' => ['Included'],

                'AI Agent Consulting' => ['Included'],

                'Financial Advisor' => ['Included'],
            ],
        ];

        foreach ($plans as $planName => $features) {
            $planId = DB::table('entry_meta')
                ->where('meta_key', 'plan_name')
                ->where('meta_value', $planName)
                ->value('entry_id');

            foreach ($features as $featureName => $benefits) {
                $featureId = DB::table('entry_meta')
                    ->where('meta_key', 'name')
                    ->where('meta_value', $featureName)
                    ->value('entry_id');

                foreach ($benefits as $benefitText) {
                    $benefitId = DB::table('entries')->insertGetId([
                        'collection_id' => $benefitCollectionId,
                        'status' => 'published',
                        'created_by' => $createdBy,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    DB::table('entry_meta')->insert([
                        'entry_id' => $benefitId,
                        'meta_key' => 'benefit_text',
                        'meta_value' => $benefitText,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    DB::table('entry_relations')->insert([
                        [
                            'parent_entry_id' => $planId,
                            'child_entry_id' => $benefitId,
                            'relation_type' => 'plan_benefit',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ],
                        [
                            'parent_entry_id' => $featureId,
                            'child_entry_id' => $benefitId,
                            'relation_type' => 'feature_benefit',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ],
                    ]);
                }
            }
        }
    }
}
