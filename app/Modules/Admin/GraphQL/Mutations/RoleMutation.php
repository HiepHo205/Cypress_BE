<?php

namespace App\Modules\Admin\GraphQL\Mutations;

use App\Core\Models\Role;
use App\Core\Models\User;
use App\Core\Services\Role\RoleService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class RoleMutation
{
    public function __construct(
        protected RoleService $roleService
    ) {}

    public function update($_, array $args): Role
    {
        return $this->roleService->update($args);
    }

    public function delete($_, array $args): bool
    {
        return $this->roleService->delete($args);
    }
    public function transferAdmin($_, array $args): bool
    {
        return $this->roleService->transferAdmin($args);
    }
    public function changeUserRole($_, array $args): bool
    {
        $user = User::findOrFail($args['userId']);

        // tìm role theo name
        $role = Role::where('name', $args['role'])->firstOrFail();

        $user->roles()->sync([$role->id]);

        return true;
    }
}
