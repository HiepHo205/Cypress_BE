<?php

namespace App\Modules\Admin\GraphQL\Mutations;

use App\Core\Services\Auth\AuthService;
use App\Modules\Api\GraphQL\Validators\LoginInputValidator;
use Exception;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Tymon\JWTAuth\Facades\JWTAuth;

class AdminAuthMutation
{
    protected AuthService $authService;
    protected LoginInputValidator $validator;

    public function __construct(
        AuthService $authService,
        LoginInputValidator $validator
    ) {
        $this->authService = $authService;
        $this->validator = $validator;
    }


    public function login(
        $rootValue,
        array $args,
        GraphQLContext $context,
        ResolveInfo $resolveInfo,
    ): array {

        $data = $this->validator->validate([
            'email' => $args['email'],
            'password' => $args['password'],
        ]);

        $result = $this->authService->login($data);

        if (!($result['status'] ?? false)) {
            return [
                'status' => false,
                'message' => $result['message'] ?? 'Login failed.',
                'access_token' => null,
                'token_type' => null,
                'expires_in' => null,
                'user' => null,
            ];
        }

        $user = $result['user'];

        if (! $user->hasRole('admin')) {
            return [
                'status' => false,
                'message' => 'You do not have permission to access admin.',
                'access_token' => null,
                'token_type' => null,
                'expires_in' => null,
                'user' => null,
            ];
        }

        return [
            'status' => true,
            'message' => $result['message'] ?? 'Login successful.',
            'access_token' => $result['access_token'],
            'token_type' => $result['token_type'],
            'expires_in' => $result['expires_in'],
            'user' => $user,
        ];
    }

 public function logout(
        $rootValue,
        array $args,
        GraphQLContext $context,
        ResolveInfo $resolveInfo,
    ) {

        return [
            'status' => true,
            'message' => 'Logged out successfully.',
        ];
        try {
            JWTAuth::invalidate(JWTAuth::getToken());

            return [
                'status' => true,
                'message' => 'Logged out successfully.',
            ];
        } catch (Exception $e) {
            return [
                'status' => false,
                'message' => 'Logout failed: ' . $e->getMessage(),
            ];
        }
    }
}
