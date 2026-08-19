<?php

namespace Database\Seeders;

use App\Core\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CollectionSeeder::class,
            PlanSeeder::class,
            FeatureSeeder::class,
            BenefitSeeder::class,
            CompanySeeder::class,
            SubscriptionSeeder::class,
            HomepageSeeder::class,
            PermissionSeeder::class,
            NewsPageSeeder::class,
            CaseStudySeeder::class,
        ]);

        $this->call([PermissionSeeder::class]);
    }
}
