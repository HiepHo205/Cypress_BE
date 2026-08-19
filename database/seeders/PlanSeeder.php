<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $createdBy = DB::table('users')->orderBy('id')->value('id');

        if (!$createdBy) {
            throw new \RuntimeException(
                'No user found to assign as plan creator.',
            );
        }

        $collectionId = DB::table('collections')
            ->where('collection_name', 'plans')
            ->value('id');

        $plans = [
            [
                'name' => 'Startup',
                'price' => '1900000',
            ],
            [
                'name' => 'Scale Up',
                'price' => '4900000',
            ],
            [
                'name' => 'IPO',
                'price' => 'Custom',
            ],
        ];

        foreach ($plans as $plan) {
            $entryId = DB::table('entries')->insertGetId([
                'collection_id' => $collectionId,
                'status' => 'published',
                'created_by' => $createdBy,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('entry_meta')->insert([
                [
                    'entry_id' => $entryId,
                    'meta_key' => 'plan_name',
                    'meta_value' => $plan['name'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'entry_id' => $entryId,
                    'meta_key' => 'price',
                    'meta_value' => $plan['price'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }
}
