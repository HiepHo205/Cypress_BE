<?php

namespace App\Core\Services\Auth;

use App\Core\Models\Role;
use App\Core\Repositories\Contracts\UserRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
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
            'password' => Hash::make($data['password']),
        ]);

        $role = Role::where('name', 'user')->first();

        if (!$role) {
            throw new Exception('Default role "user" not found.');
        }

        $user->roles()->sync([$role->id]);

        $user->load('roles');

        return [
            'status' => true,
            'message' => 'Registration completed successfully.',
            'user' => $user->load('roles'),
        ];
    }

    public function login(array $credentials): array
    {
        if (!($token = JWTAuth::attempt($credentials))) {
            return [
                'status' => false,
                'message' => 'Invalid email or password.',
                'access_token' => null,
                'token_type' => null,
                'expires_in' => null,
                'user' => null,
            ];
        }

        $user = JWTAuth::user();

        $role = $user->roles->first()?->name;

        return [
            'status' => true,
            'message' =>
                $role === 'admin'
                    ? 'Admin login successful.'
                    : 'User login successful.',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => JWTAuth::factory()->getTTL(),
            'user' => $user,
        ];
    }
}
