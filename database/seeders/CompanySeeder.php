<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $companyCollectionId = DB::table('collections')
            ->where('collection_name', 'companies')
            ->value('id');

        $companyId = DB::table('entries')->insertGetId([
            'collection_id' => $companyCollectionId,
            'status' => 'published',
            'created_by' => 6,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('entry_meta')->insert([
            [
                'entry_id' => $companyId,
                'meta_key' => 'company_name',
                'meta_value' => 'Ciiclo Technology',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'entry_id' => $companyId,
                'meta_key' => 'business_email',
                'meta_value' => 'contact@ciiclo.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}