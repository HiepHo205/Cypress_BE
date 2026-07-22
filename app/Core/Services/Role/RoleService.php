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
            throw new Exception(
                'You do not have permission to create roles.'
            );
        }

        Validator::make(
            $args,
            [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    'unique:roles,name',
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

        return Role::create([
            'name' => $args['name'],
            'description' => $args['description'] ?? null,
        ]);
    }

    public function update(array $args): Role
    {
        $user = Auth::user();

        if (!$user instanceof User || !$user->hasRole('admin')) {
            throw new Exception(
                'You do not have permission to update roles.'
            );
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
            $role->name === 'admin' &&
            $args['name'] !== 'admin'
        ) {
            throw new Exception(
                'The Admin role cannot be renamed.'
            );
        }

        $role->update([
            'name' => $args['name'],
            'description' => $args['description'] ?? $role->description,
        ]);

        return $role->fresh();
    }

    public function delete(array $args): bool
    {
        $user = Auth::user();

        if (!$user instanceof User || !$user->hasRole('admin')) {
            throw new Exception(
                'You do not have permission to delete roles.'
            );
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
            throw new Exception(
                'The Admin role cannot be deleted.'
            );
        }

        $role->delete();

        return true;
    }
}
