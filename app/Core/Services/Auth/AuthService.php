<?php

namespace App\Core\Services\Auth;

use App\Core\Repositories\Contracts\UserRepositoryInterface;
use Exception;

class AuthService
{
    protected UserRepositoryInterface $userRepo;

    public function __construct(UserRepositoryInterface $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function register(array $data): array
    {
        $existingUser = $this->userRepo->findByEmail($data['email']);
        if ($existingUser) {
            throw new Exception('Email already exists.');
        }

        $user = $this->userRepo->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        return [
            'status' => true,
            'message' => 'Registration completed successfully.',
            'user' => $user,
        ];
    }
}
