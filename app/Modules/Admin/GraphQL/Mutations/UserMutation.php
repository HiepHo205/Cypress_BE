<?php

namespace App\Modules\Admin\GraphQL\Mutations;

use App\Core\Models\User;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Hash;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class UserMutation
{
    public function create($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): array
    {
        $user = new User();
        $user->name = $args['input']['name'];
        $user->email = $args['input']['email'];
        $user->password = Hash::make($args['input']['password']);
        $user->save();

        return ['user' => $user];
    }

    public function update($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): array
    {
        $user = User::findOrFail($args['id']);

        if (isset($args['input']['name'])) {
            $user->name = $args['input']['name'];
        }

        if (isset($args['input']['email'])) {
            $user->email = $args['input']['email'];
        }

        if (!empty($args['input']['password'])) {
            $user->password = Hash::make($args['input']['password']);
        }

        $user->save();

        return ['user' => $user];
    }

    public function delete($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): array
    {
        $user = User::findOrFail($args['id']);
        $user->delete();

        return ['user' => $user];
    }
}
