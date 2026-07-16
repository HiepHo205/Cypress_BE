<?php

namespace App\Core\Services\Auth;

use Illuminate\Support\Facades\Auth;

class AuthService
{
    public function login(array $credentials): array
    {
        if (! $token = Auth::guard('api')->attempt($credentials)) {

            return [
                'status' => false,
                'message' => 'Email or password is incorrect.',
                'access_token' => null,
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
