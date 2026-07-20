<?php

namespace App\Modules\Admin\GraphQL\Mutations;

use App\Core\Models\User;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class UserMutation
{
    public function create($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): array
    {
        $user = new User();
        $user->name = $args['input']['name'];
        $user->email = $args['input']['email'];
        $user->password = $args['input']['password'];
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
            $user->password = $args['input']['password'];
        }

        if (array_key_exists('status', $args['input'])) {
            $user->status = $args['input']['status'];
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

    public function deactivate($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): array
    {
        $user = User::findOrFail($args['id']);
        $user->status = 'unactive';
        $user->save();

        return ['user' => $user];
    }
}
