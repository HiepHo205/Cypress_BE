<?php

namespace App\Modules\Admin\GraphQL\Mutations;

use Exception;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Tymon\JWTAuth\Facades\JWTAuth;

class AdminAuthMutation
{
    public function login(
        $rootValue,
        array $args,
        GraphQLContext $context,
        ResolveInfo $resolveInfo,
    ) {
        return [
            'status' => false,
            'message' => 'This feature is currently under development.',
        ];
    }

 public function logout(
        $rootValue,
        array $args,
        GraphQLContext $context,
        ResolveInfo $resolveInfo,
    ) {
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
