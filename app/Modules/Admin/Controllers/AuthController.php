<?php

namespace App\Modules\Auth\Controllers;

use Illuminate\Routing\Controller;
use App\Core\Services\Auth\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request, AuthService $authService)
    {
        return response()->json(
            $authService->login(
                $request->only('email', 'password')
            )
        );
    }
}
