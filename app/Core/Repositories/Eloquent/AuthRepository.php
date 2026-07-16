<?php

namespace App\Core\Repositories\Eloquent;

use App\Core\Models\User;
use App\Core\Repositories\Contracts\AuthRepositoryInterface;


class AuthRepository implements AuthRepositoryInterface
{

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

}