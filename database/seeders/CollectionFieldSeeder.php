<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CollectionFieldSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('collection_fields')->insert([
            [
                'id' => 1,
                'collection_id' => 1,
                'field_name' => 'price',
                'field_label' => 'Price',
                'field_type' => 'number',
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'collection_id' => 1,
                'field_name' => 'contact_required',
                'field_label' => 'Contact Required',
                'field_type' => 'boolean',
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'collection_id' => 1,
                'field_name' => 'seat_feature',
                'field_label' => 'Seat Feature',
                'field_type' => 'text',
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'collection_id' => 1,
                'field_name' => 'meeting_room_feature',
                'field_label' => 'Meeting Room Feature',
                'field_type' => 'text',
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'collection_id' => 1,
                'field_name' => 'screen_feature',
                'field_label' => 'Screen Feature',
                'field_type' => 'text',
                'sort_order' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'collection_id' => 1,
                'field_name' => 'printing_feature',
                'field_label' => 'Printing Feature',
                'field_type' => 'text',
                'sort_order' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 7,
                'collection_id' => 1,
                'field_name' => 'marketing_feature',
                'field_label' => 'Marketing Feature',
                'field_type' => 'text',
                'sort_order' => 7,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 8,
                'collection_id' => 1,
                'field_name' => 'ai_agent_feature',
                'field_label' => 'AI Agent Feature',
                'field_type' => 'text',
                'sort_order' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}