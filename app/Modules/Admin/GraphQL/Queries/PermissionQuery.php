<?php

namespace App\Modules\Admin\GraphQL\Queries;

use App\Core\Models\Permission;

class PermissionQuery
{
    public function permissions()
    {
        return Permission::all();
    }
}