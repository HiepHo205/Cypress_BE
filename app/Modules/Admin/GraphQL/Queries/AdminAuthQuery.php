<?php

namespace App\Modules\Admin\GraphQL\Queries;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Illuminate\Support\Facades\Auth;

class AdminAuthQuery
{
    public function me(
        $rootValue,
        array $args,
        GraphQLContext $context,
        ResolveInfo $resolveInfo,
    ) {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return [
                'status' => false,
                'message' => 'Unauthorized',
                'user' => null,
            ];
        }

        return [
            'status' => true,
            'message' => 'Get user successfully',
            'user' => $user,
        ];
    }
}
