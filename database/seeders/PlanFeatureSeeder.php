<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanFeatureSeeder extends Seeder
{
    public function run(): void
    {
        $mapping = [

            'Startup' => [
                'Workspace',
                'Meeting Room',
                'Printing Service',
                'Screen Rental',
                'Marketing Services',
            ],

            'Scale Up' => [
                'Workspace',
                'Meeting Room',
                'Printing Service',
                'Screen Rental',
                'Marketing Services',
                'Operations Consulting',
                'Software Consulting',
                'AI Agent Consulting',
            ],

            'IPO' => [
                'Workspace',
                'Meeting Room',
                'Printing Service',
                'Screen Rental',
                'Marketing Services',
                'Operations Consulting',
                'Software Consulting',
                'AI Agent Consulting',
                'Financial Advisor',
            ],
        ];

        foreach ($mapping as $planName => $features) {

            $planId = DB::table('entry_meta')
                ->where('meta_key', 'plan_name')
                ->where('meta_value', $planName)
                ->value('entry_id');

            foreach ($features as $featureName) {

                $featureId = DB::table('entry_meta')
                    ->where('meta_key', 'name')
                    ->where('meta_value', $featureName)
                    ->value('entry_id');

                DB::table('entry_relations')->insert([
                    'parent_entry_id' => $planId,
                    'child_entry_id' => $featureId,
                    'relation_type' => 'plan_feature',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}