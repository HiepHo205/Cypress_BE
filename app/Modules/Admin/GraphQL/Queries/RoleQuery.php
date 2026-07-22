<?php

namespace App\Modules\Admin\GraphQL\Queries;

use App\Core\Models\Role;

class RoleQuery
{
    public function roles($_, array $args)
    {
        return Role::all();
    }
}
