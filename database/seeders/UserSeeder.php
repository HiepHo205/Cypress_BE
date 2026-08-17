<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->updateOrInsert(
            ['email' => 'admin@cypresshub.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('12345678'),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        );
    }
}
