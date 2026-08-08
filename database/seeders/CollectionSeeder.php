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
                'collection_name' => 'companies',
                'api_endpoint' => 'companies',
                'is_system_type' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'collection_name' => 'plans',
                'api_endpoint' => 'plans',
                'is_system_type' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'collection_name' => 'subscriptions',
                'api_endpoint' => 'subscriptions',
                'is_system_type' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'collection_name' => 'features',
                'api_endpoint' => 'features',
                'is_system_type' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'collection_name' => 'benefits',
                'api_endpoint' => 'benefits',
                'is_system_type' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}