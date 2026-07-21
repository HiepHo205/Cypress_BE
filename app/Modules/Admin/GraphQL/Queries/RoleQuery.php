<?php

namespace App\Modules\Admin\GraphQL\Queries;

use App\Core\Models\Role;

class RoleQuery
{
    public function all($_, array $args)
    {
        return Role::with('permissions')->get();
    }
}