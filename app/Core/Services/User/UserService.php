<?php
namespace App\Core\Services\User;

use App\Core\Models\User;
use App\Core\Models\Role;
use Exception;

class UserService
{
    public function transferAdmin(int $newAdminId): bool
    {
        $adminRole = Role::where('name', 'admin')->firstOrFail();
        $userRole  = Role::where('name', 'user')->firstOrFail();

        $currentAdmin = User::whereHas('roles', function ($q) {
            $q->where('name', 'admin');
        })->first();

        if (!$currentAdmin) {
            throw new Exception('Current admin not found.');
        }

        if ($currentAdmin->id == $newAdminId) {
            throw new Exception('This user is already the admin.');
        }
        $currentAdmin->roles()->sync([$userRole->id]);

        $newAdmin = User::findOrFail($newAdminId);
        $newAdmin->roles()->sync([$adminRole->id]);

        return true;
    }
}