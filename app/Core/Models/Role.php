<?php

namespace App\Core\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['role_name', 'description'])]

class Role extends Model
{
    protected $table = 'roles';
}
