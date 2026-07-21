<?php

namespace App\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Models\Permission;

class Role extends Model
{
    protected $fillable = [
        'name'
    ];


    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'user_roles'
        );
    }


    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            Permission::class,
            'role_permissions',
            'role_id',
            'permission_id'
        );
    }
}
