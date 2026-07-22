<?php

namespace App\Core\Services\User;

use App\Core\Models\User;
use App\Core\Models\Role;
use Exception;

class UserService
{
    public function transferAdmin(int $newAdminId): bool
    {
        $adminRole = Role::where('role_name', 'admin')->firstOrFail();
        $userRole  = Role::where('role_name', 'user')->firstOrFail();

        $currentAdmin = User::where('role_id', $adminRole->id)->first();

        if (!$currentAdmin) {
            throw new Exception('Current admin not found.');
        }

        if ($currentAdmin->id == $newAdminId) {
            throw new Exception('This user is already the admin.');
        }

        $currentAdmin->update([
            'role_id' => $userRole->id
        ]);

        $newAdmin = User::findOrFail($newAdminId);

        $newAdmin->update([
            'role_id' => $adminRole->id
        ]);

        return true;
    }
}
