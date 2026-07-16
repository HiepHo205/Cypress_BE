<?php
namespace App\Modules\Admin\GraphQL\Mutations;

use App\Core\Services\Auth\AuthService;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class AdminAuthMutation
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(
        $rootValue,
        array $args,
        GraphQLContext $context,
        ResolveInfo $resolveInfo,
    ) {

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
    }
}