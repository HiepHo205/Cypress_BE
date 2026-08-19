<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FeatureSeeder extends Seeder
{
    public function run(): void
    {
        $createdBy = DB::table('users')->orderBy('id')->value('id');

        if (!$createdBy) {
            throw new \RuntimeException('No user found for feature seeding.');
        }

        $collectionId = DB::table('collections')
            ->where('collection_name', 'features')
            ->value('id');

        $features = [
            ['name' => 'Workspace', 'group' => 'core'],
            ['name' => 'Meeting Room', 'group' => 'core'],
            ['name' => 'Printing Service', 'group' => 'core'],
            ['name' => 'Screen Rental', 'group' => 'core'],

            ['name' => 'Marketing Services', 'group' => 'usp'],
            ['name' => 'Operations Consulting', 'group' => 'usp'],
            ['name' => 'Software Consulting', 'group' => 'usp'],
            ['name' => 'AI Agent Consulting', 'group' => 'usp'],
            ['name' => 'Financial Advisor', 'group' => 'usp'],
        ];

        foreach ($features as $feature) {
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
                    'meta_key' => 'name',
                    'meta_value' => $feature['name'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'entry_id' => $entryId,
                    'meta_key' => 'group',
                    'meta_value' => $feature['group'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }
}
