<?php

namespace App\Core\Repositories\Contracts;

use App\Core\Models\User;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function findByEmail(string $email): ?User;
}
