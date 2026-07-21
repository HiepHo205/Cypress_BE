<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GraphQLUserMutationTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_user_mutation_returns_a_user_payload(): void
    {
        $query = <<<'GRAPHQL'
mutation CreateUser($input: CreateUserInput!) {
  createUser(input: $input) {
    user {
      id
      name
      email
    }
  }
}
GRAPHQL;

        $response = $this->postJson('/graphql', [
            'query' => $query,
            'variables' => [
                'input' => [
                    'name' => 'Jane Doe',
                    'email' => 'jane@example.com',
                    'password' => 'secret12345',
                ],
            ],
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.createUser.user.name', 'Jane Doe');
        $response->assertJsonPath('data.createUser.user.email', 'jane@example.com');
        $this->assertDatabaseHas('users', ['email' => 'jane@example.com']);
    }

    public function test_update_user_mutation_can_deactivate_a_user(): void
    {
        $user = \App\Core\Models\User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'status' => 'active',
        ]);

        $query = <<<'GRAPHQL'
mutation UpdateUser($id: ID!, $input: UpdateUserInput!) {
  updateUser(id: $id, input: $input) {
    user {
      id
      status
    }
  }
}
GRAPHQL;

        $response = $this->postJson('/graphql', [
            'query' => $query,
            'variables' => [
                'id' => (string) $user->id,
                'input' => ['status' => 'unactive'],
            ],
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.updateUser.user.status', 'unactive');
        $this->assertDatabaseHas('users', ['id' => $user->id, 'status' => 'unactive']);
    }
}
