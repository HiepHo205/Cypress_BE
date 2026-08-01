<?php

namespace App\Modules\Admin\GraphQL\Mutations;

use App\Core\Models\User;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Illuminate\Support\Facades\Hash;
use Exception;

class UserMutation
{
    private function authorize(string $permission): void
    {
        $user = auth()->user();

        if (!$user) {
            throw new Exception('Unauthenticated');
        }

        if (!$user->hasPermission($permission)) {
            throw new Exception('Permission denied');
        }
    }

    public function create(
        $rootValue,
        array $args,
        GraphQLContext $context,
        ResolveInfo $resolveInfo
    ): array {
        $this->authorize('user.create');

        $user = new User();
        $user->name = $args['input']['name'];
        $user->email = $args['input']['email'];
        $user->password = Hash::make($args['input']['password']);
        $user->role_id = $args['input']['role_id'];
        $user->save();

        return ['user' => $user];
    }

    public function update(
        $rootValue,
        array $args,
        GraphQLContext $context,
        ResolveInfo $resolveInfo
    ): array {
        $this->authorize('user.update');

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

        if (array_key_exists('status', $args['input'])) {
            $user->status = $args['input']['status'];
        }

        if (isset($args['input']['role_id'])) {
            $user->role_id = $args['input']['role_id'];
        }

        $user->save();

        return ['user' => $user];
    }

    public function delete(
        $rootValue,
        array $args,
        GraphQLContext $context,
        ResolveInfo $resolveInfo,
    ): array {
        $this->authorize('user.delete');

        $user = User::findOrFail($args['id']);
        $user->delete();

        return ['user' => $user];
    }

    public function deactivate(
        $rootValue,
        array $args,
        GraphQLContext $context,
        ResolveInfo $resolveInfo,
    ): array {
        $this->authorize('user.deactivate');

        $user = User::findOrFail($args['id']);
        $user->status = 'unactive';
        $user->save();

        return ['user' => $user];
    }
}