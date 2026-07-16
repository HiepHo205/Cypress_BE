<?php

namespace App\Core\Services\Auth;

use App\Core\Repositories\Contracts\UserRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Auth;

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

    public function login(array $credentials): array
    {
        if (! $token = Auth::guard('api')->attempt($credentials)) {

            return [
                'status' => false,
                'message' => 'Email or password is incorrect.',
                'access_token' => "",
                'token_type' => 'bearer',
                'expires_in' => null,
                'user' => null,
            ];
        }

        return [
            'status' => true,
            'message' => 'Login successfully',
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60,
            'user' => Auth::guard('api')->user(),
        ];
    }
}
