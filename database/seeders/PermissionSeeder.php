<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['code' => 'user.view', 'description' => 'View users'],
            ['code' => 'user.create', 'description' => 'Create users'],
            ['code' => 'user.update', 'description' => 'Update users'],
            ['code' => 'user.delete', 'description' => 'Delete users'],
            ['code' => 'user.deactivate', 'description' => 'Deactivate users'],
            ['code' => 'role.view', 'description' => 'View roles'],
            ['code' => 'role.create', 'description' => 'Create roles'],
            ['code' => 'role.update', 'description' => 'Update roles'],
            ['code' => 'role.delete', 'description' => 'Delete roles'],
            ['code' => 'plan.create', 'description' => 'Create plans'],
            ['code' => 'plan.update', 'description' => 'Update plans'],
            ['code' => 'plan.delete', 'description' => 'Delete plans'],
            ['code' => 'subscription.create', 'description' => 'Create subscriptions'],
            ['code' => 'subscription.update', 'description' => 'Update subscriptions'],
            ['code' => 'subscription.delete', 'description' => 'Delete subscriptions'],
            ['code' => 'feature.create', 'description' => 'Create features'],
            ['code' => 'feature.update', 'description' => 'Update features'],
            ['code' => 'feature.delete', 'description' => 'Delete features'],
            ['code' => 'benefit.create', 'description' => 'Create benefits'],
            ['code' => 'benefit.update', 'description' => 'Update benefits'],
            ['code' => 'benefit.delete', 'description' => 'Delete benefits'],
            ['code' => 'permission.assign','description' => 'Assign permissions to roles'],
            ['code' => 'permission.unassign','description' => 'Revoke permissions from roles'],

        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['code' => $permission['code']],
                [
                    'description' => $permission['description'],
                ],
            );
        }
    }
}
