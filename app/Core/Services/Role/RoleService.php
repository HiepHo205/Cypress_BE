<?php

namespace App\Core\Services\Role;

use App\Core\Models\Role;
use App\Core\Models\User;
use App\Modules\Admin\GraphQL\Validators\DeleteRoleValidator;
use Illuminate\Validation\Rule;
use Exception;
use Illuminate\Support\Facades\Auth;
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

        Validator::make($args, [
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
        ])->validate();

        $role = Role::findOrFail($args['id']);

        // Không cho sửa role mặc định
        if (in_array($role->name, ['admin', 'user'])) {
            throw new Exception('Default roles cannot be updated.');
        }

        $role->update([
            'name' => $args['name'],
            'description' => $args['description'] ?? $role->description,
        ]);

        return $role->fresh();
    }
}
