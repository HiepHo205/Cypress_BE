<?php

namespace App\Core\Services\Role;

use App\Core\Models\Role;
use App\Core\Models\User;
use App\Modules\Admin\GraphQL\Validators\DeleteRoleValidator;
use Illuminate\Validation\Rule;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RoleService
{
    public function delete(array $args): bool
    {
        $user = Auth::user();

        if (!$user instanceof User || !$user->hasRole('admin')) {
            throw new Exception('You do not have permission to delete roles.');
        }

        Validator::make(
            $args,
            (new DeleteRoleValidator())->rules()
        )->validate();

        $role = Role::findOrFail($args['id']);

        if (in_array($role->name, ['admin', 'user'])) {
            throw new Exception('Default roles cannot be deleted.');
        }

        $role->delete();

        return true;
    }

    public function update(array $args): Role
    {
        $user = Auth::user();

        if (!$user instanceof User || !$user->hasRole('admin')) {
            throw new Exception('You do not have permission to update roles.');
        }

        Validator::make(
            $args,
            [
                'id' => [
                    'required',
                    'integer',
                    'exists:roles,id',
                ],
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('roles', 'name')->ignore($args['id']),
                ],
                'description' => [
                    'nullable',
                    'string',
                ],
            ],
            [
                'name.unique' => 'Role name already exists.',
            ]
        )->validate();

        $role = Role::findOrFail($args['id']);
        if (
            in_array($role->name, ['admin', 'user']) &&
            $role->name !== $args['name']
        ) {
            throw new Exception(
                'Default roles cannot be renamed. Please change the user\'s role instead.'
            );
        }

        $role->update([
            'name' => $args['name'],
            'description' => $args['description'] ?? $role->description,
        ]);

        return $role->fresh();
    }
    public function transferAdmin(array $args): bool
    {
        /** @var User|null $currentUser */
        $currentUser = Auth::user();

        if (!$currentUser instanceof User) {
            throw new Exception('Unauthenticated.');
        }

        if (!$currentUser->hasRole('admin')) {
            throw new Exception('Only admin can transfer admin role.');
        }

        $newAdmin = User::findOrFail($args['newAdminId']);

        if ($currentUser->id === $newAdmin->id) {
            throw new Exception('You are already the admin.');
        }
        if ($newAdmin->hasRole('admin')) {
            throw new Exception('Selected user is already an admin.');
        }

        $adminRole = Role::where('name', 'admin')->firstOrFail();
        $userRole = Role::where('name', 'user')->firstOrFail();

        DB::transaction(function () use (
            $currentUser,
            $newAdmin,
            $adminRole,
            $userRole
        ) {

            $currentUser->roles()->sync([$userRole->id]);

            $newAdmin->roles()->sync([$adminRole->id]);
        });

        return true;
    }

    public function changeUserRole(array $args): bool
    {
        /** @var User|null $currentUser */
        $currentUser = Auth::user();

        if (!$currentUser instanceof User || !$currentUser->hasRole('admin')) {
            throw new Exception('Only admin can change roles.');
        }

        $user = User::findOrFail($args['userId']);

        $newRole = Role::where('name', $args['roleName'])->firstOrFail();

        DB::transaction(function () use ($user, $newRole) {

            if ($newRole->name === 'admin') {

                $adminRole = Role::where('name', 'admin')->first();

                $userRole = Role::where('name', 'user')->first();

                $oldAdmin = User::whereHas('roles', function ($q) {
                    $q->where('name', 'admin');
                })->first();

                if ($oldAdmin && $oldAdmin->id !== $user->id) {
                    $oldAdmin->roles()->sync([$userRole->id]);
                }
            }

            $user->roles()->sync([$newRole->id]);
        });

        return true;
    }
}
