<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        $subscriptionCollectionId = DB::table('collections')
            ->where('collection_name', 'subscriptions')
            ->value('id');

        $companyId = DB::table('entries')
            ->join(
                'collections',
                'collections.id',
                '=',
                'entries.collection_id'
            )
            ->where('collections.collection_name', 'companies')
            ->value('entries.id');

        $startupPlanId = DB::table('entry_meta')
            ->where('meta_key', 'plan_name')
            ->where('meta_value', 'Startup')
            ->value('entry_id');

        $subscriptionId = DB::table('entries')->insertGetId([
            'collection_id' => $subscriptionCollectionId,
            'status' => 'published',
            'created_by' => 6,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('entry_meta')->insert([
            [
                'entry_id' => $subscriptionId,
                'meta_key' => 'start_date',
                'meta_value' => now()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'entry_id' => $subscriptionId,
                'meta_key' => 'end_date',
                'meta_value' => now()->addMonth()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'entry_id' => $subscriptionId,
                'meta_key' => 'status',
                'meta_value' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('entry_relations')->insert([
            [
                'parent_entry_id' => $companyId,
                'child_entry_id' => $subscriptionId,
                'relation_type' => 'company_subscription',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'parent_entry_id' => $subscriptionId,
                'child_entry_id' => $startupPlanId,
                'relation_type' => 'subscription_plan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}