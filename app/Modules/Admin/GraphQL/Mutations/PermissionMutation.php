<?php

namespace App\Modules\Admin\GraphQL\Mutations;

use App\Core\Models\User;
use Exception;

class PermissionMutation
{
    public function assignPermission($_, array $args): bool
    {
        $currentUser = auth()->user();

        if (!$currentUser) {
            throw new Exception('Unauthenticated');
        }

        if (!$currentUser->hasPermission('permission.assign')) {
            throw new Exception('Permission denied');
        }

        $targetUser = User::findOrFail($args['userId']);

        $targetUser->permissions()->syncWithoutDetaching([
            $args['permissionId']
        ]);

        return true;
    }

    public function unassignPermission($_, array $args): bool
    {
        $currentUser = auth()->user();

        if (!$currentUser) {
            throw new Exception('Unauthenticated');
        }

        if (!$currentUser->hasPermission('permission.unassign')) {
            throw new Exception('Permission denied');
        }

        $targetUser = User::findOrFail($args['userId']);

        $targetUser->permissions()->detach(
            $args['permissionId']
        );

        return true;
    }
}