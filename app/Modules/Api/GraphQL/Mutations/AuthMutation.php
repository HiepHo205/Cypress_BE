<?php

namespace App\Modules\Api\GraphQL\Mutations;

use App\Core\Services\Auth\AuthService;
use App\Modules\Api\GraphQL\Validators\LoginInputValidator;
use Exception;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class AuthMutation
{
    protected AuthService $authService;
    protected LoginInputValidator $loginValidator;

    public function __construct(
        AuthService $authService,
        LoginInputValidator $loginValidator,
    ) {
        $this->authService = $authService;
        $this->loginValidator = $loginValidator;
    }

    public function register(mixed $root, array $args): array
    {
        try {
            return $this->authService->register($args);
        } catch (Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage(),
                'user' => null,
            ];
        }
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
}
