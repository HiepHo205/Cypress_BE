<?php

namespace App\Core\Repositories\Contracts;

use App\Core\Models\User;

interface AuthRepositoryInterface
{
    public function findByEmail(string $email): ?User;
}