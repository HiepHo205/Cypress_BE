<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EntryMetaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('entry_meta')->insert([

            // STARTUP

            ['entry_id'=>1,'field_id'=>1,'meta_value'=>'1900000'],
            ['entry_id'=>1,'field_id'=>2,'meta_value'=>'0'],
            ['entry_id'=>1,'field_id'=>3,'meta_value'=>'Chỗ ngồi linh hoạt'],
            ['entry_id'=>1,'field_id'=>4,'meta_value'=>'Miễn phí 3h/tháng'],
            ['entry_id'=>1,'field_id'=>5,'meta_value'=>'Giá tiêu chuẩn'],
            ['entry_id'=>1,'field_id'=>6,'meta_value'=>'Miễn phí 10 trang/tháng'],
            ['entry_id'=>1,'field_id'=>7,'meta_value'=>'1 bài viết/tháng'],
            ['entry_id'=>1,'field_id'=>8,'meta_value'=>'Gemini AI cơ bản'],

            // SCALE UP

            ['entry_id'=>2,'field_id'=>1,'meta_value'=>'4900000'],
            ['entry_id'=>2,'field_id'=>2,'meta_value'=>'0'],
            ['entry_id'=>2,'field_id'=>3,'meta_value'=>'Chỗ ngồi linh hoạt'],
            ['entry_id'=>2,'field_id'=>4,'meta_value'=>'Miễn phí 5h/tháng'],
            ['entry_id'=>2,'field_id'=>5,'meta_value'=>'Giảm giá 10%'],
            ['entry_id'=>2,'field_id'=>6,'meta_value'=>'Miễn phí 30 trang/tháng'],
            ['entry_id'=>2,'field_id'=>7,'meta_value'=>'4 bài viết/tháng'],
            ['entry_id'=>2,'field_id'=>8,'meta_value'=>'Google Gemini AI Pro'],

            // IPO

            ['entry_id'=>3,'field_id'=>1,'meta_value'=>'0'],
            ['entry_id'=>3,'field_id'=>2,'meta_value'=>'1'],
            ['entry_id'=>3,'field_id'=>3,'meta_value'=>'Miễn phí tùy khóa'],
            ['entry_id'=>3,'field_id'=>4,'meta_value'=>'Miễn phí 10h/tháng'],
            ['entry_id'=>3,'field_id'=>5,'meta_value'=>'Giảm giá 20%'],
            ['entry_id'=>3,'field_id'=>6,'meta_value'=>'Miễn phí 50 trang/tháng'],
            ['entry_id'=>3,'field_id'=>7,'meta_value'=>'10 bài viết/tháng'],
            ['entry_id'=>3,'field_id'=>8,'meta_value'=>'ChatGPT Pro + Gemini Pro'],
        ]);
    }
}