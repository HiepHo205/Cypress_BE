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

        $response = $this->postJson('/graphql/graphql', [
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
}
