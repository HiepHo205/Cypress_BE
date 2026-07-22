<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EntrySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('entries')->insert([
            [
                'id' => 1,
                'collection_id' => 1,
                'title' => 'Startup',
                'slug' => 'startup',
                'status' => 'published',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'collection_id' => 1,
                'title' => 'Scale Up',
                'slug' => 'scale-up',
                'status' => 'published',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'collection_id' => 1,
                'title' => 'IPO',
                'slug' => 'ipo',
                'status' => 'published',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}