<?php

namespace App\Modules\API\Controllers;

use Illuminate\Routing\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    public function logout()
    {
        try {
            $token = JWTAuth::getToken();

            if (!$token) {
                return response()->json([
                    'status' => false,
                    'message' => 'Token not provided'
                ], 400);
            }

            $user = JWTAuth::authenticate();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found'
                ], 401);
            }

            $message = 'User logout successfully';

            if ($user->role?->role_name === 'admin') {
                $message = 'Admin logout successfully';
            }

            JWTAuth::invalidate($token);

            return response()->json([
                'status' => true,
                'message' => $message
            ]);
        } catch (JWTException $e) {

            return response()->json([
                'status' => false,
                'message' => 'Logout failed'
            ], 401);
        }
    }
}
