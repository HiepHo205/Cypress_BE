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
        LoginInputValidator $validator,
    ) {
        $this->authService = $authService;
        $this->validator = $validator;
    }

    public function login(
        mixed $root,
        array $args,
        GraphQLContext $context,
        ResolveInfo $resolveInfo,
    ): array {
        try {
            $result = $this->authService->login([
                'email' => $args['email'],
                'password' => $args['password'],
            ]);

            if (!$result['status']) {
                return [
                    'status' => false,
                    'message' => $result['message'],
                    'access_token' => null,
                    'token_type' => null,
                    'expires_in' => null,
                    'user' => null,
                ];
            }

            return [
                'status' => true,
                'message' => $result['message'],
                'access_token' => $result['access_token'],
                'token_type' => $result['token_type'],
                'expires_in' => $result['expires_in'],
                'user' => $result['user'],
            ];
        } catch (Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage(),
                'access_token' => null,
                'token_type' => null,
                'expires_in' => null,
                'user' => null,
            ];
        }
    }

    public function logout(
        $rootValue,
        array $args,
        GraphQLContext $context,
        ResolveInfo $resolveInfo,
    ): array {
        try {
            $this->authService->logout();

            return [
                'status' => true,
                'message' => 'Logged out successfully.',
            ];
        } catch (Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
