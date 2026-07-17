<?php

namespace App\Core\Services\Auth;

use App\Core\Repositories\Contracts\UserRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

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

    public function logout(): void
    {
        $token = JWTAuth::getToken();

        if (!$token) {
            throw new Exception('Token not provided');
        }

        JWTAuth::invalidate($token);
    }
}
