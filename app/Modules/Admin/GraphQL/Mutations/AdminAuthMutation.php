<?php

namespace App\Modules\Admin\GraphQL\Mutations;

use App\Core\Models\User;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Auth;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class AdminAuthMutation
{
    public function login(
        $rootValue,
        array $args,
        GraphQLContext $context,
        ResolveInfo $resolveInfo,
    ) {
        $credentials = [
            'email' => $args['email'],
            'password' => $args['password'],
        ];

        if (!Auth::attempt($credentials)) {
            return [
                'status' => false,
                'message' => 'Invalid email or password.',
                'access_token' => null,
                'user' => null,
            ];
        }

        $user = User::find(Auth::id());

        $token = bin2hex(random_bytes(40));

        if (schema_has_column('users', 'api_token') && $user) {
            $user->api_token = $token;
            $user->save();
        }

        return [
            'status' => true,
            'message' => 'Login successfully!',
            'access_token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'status' => $user->status ?? 'active',
            ],
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

if (!function_exists('schema_has_column')) {
    function schema_has_column($table, $column)
    {
        return \Illuminate\Support\Facades\Schema::hasColumn($table, $column);
    }
}
