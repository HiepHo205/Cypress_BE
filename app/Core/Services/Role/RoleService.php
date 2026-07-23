<?php

namespace App\Core\Services\Role;

use App\Core\Models\Role;
use App\Core\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RoleService
{
    public function create(array $args): Role
    {
        $user = Auth::user();

        if (!$user instanceof User || !$user->hasRole('admin')) {
            throw new Exception('You do not have permission to create roles.');
        }

        Validator::make(
            $args,
            [
                'role_name' => [
                    'required',
                    'string',
                    'max:255',
                    'unique:roles,role_name',
                ],
                'description' => [
                    'nullable',
                    'string',
                ],
            ],
            [
                'role_name.unique' => 'Role name already exists.',
            ]
        )->validate();

        return Role::create([
            'role_name' => $args['role_name'],
            'description' => $args['description'] ?? null,
        ]);
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
                'role_name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('roles', 'role_name')->ignore($args['id']),
                ],
                'description' => [
                    'nullable',
                    'string',
                ],
            ],
            [
                'role_name.unique' => 'Role name already exists.',
            ]
        )->validate();

        $role = Role::findOrFail($args['id']);

        if ($role->name === 'admin' && $args['role_name'] !== 'admin') {
            throw new Exception('The Admin role cannot be renamed.');
        }
        $role->update([
            'role_name' => $args['role_name'],
            'description' => $args['description'] ?? $role->description,
        ]);

        return $role->fresh();
    }

    public function delete(array $args): bool
    {
        $user = Auth::user();

        if (!$user instanceof User || !$user->hasRole('admin')) {
            throw new Exception('You do not have permission to delete roles.');
        }

        Validator::make(
            $args,
            [
                'id' => [
                    'required',
                    'integer',
                    'exists:roles,id',
                ],
            ]
        )->validate();

        $role = Role::findOrFail($args['id']);

        if ($role->name === 'admin') {
            throw new Exception('The Admin role cannot be deleted.');
        }

        $role->delete();

        return true;
    }
}
