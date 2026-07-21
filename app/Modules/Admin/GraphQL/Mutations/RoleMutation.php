<?php

namespace App\Modules\Admin\GraphQL\Mutations;

use App\Core\Models\Role;
use App\Core\Services\Role\RoleService;

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
}
