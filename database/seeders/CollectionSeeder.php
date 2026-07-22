<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CollectionSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('collections')->insert([
            [
                'id' => 1,
                'collection_name' => 'subscription_plans',
                'display_name' => 'Subscription Plans',
                'api_endpoint' => 'subscription-plans',
                'description' => 'Subscription packages',
                'is_system' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}