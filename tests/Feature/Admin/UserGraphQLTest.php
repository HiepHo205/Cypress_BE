<?php

namespace Tests\Feature\Admin;

use App\Core\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserGraphQLTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_query_returns_paginated_users(): void
    {
        User::factory()->create([
            'name' => 'Alice Johnson',
            'email' => 'alice@example.com',
        ]);

        $response = $this->postJson('/graphql', [
            'query' => '{ users { data { id name email } paginatorInfo { currentPage lastPage } } }',
        ]);

        $response->assertOk();
        $response->assertJsonPath('data.users.data.0.name', 'Alice Johnson');
        $response->assertJsonPath('data.users.paginatorInfo.currentPage', 1);
    }

    public function test_create_user_mutation_creates_a_user(): void
    {
        $response = $this->postJson('/graphql', [
            'query' => 'mutation CreateUser($input: CreateUserInput!) { createUser(input: $input) { user { id name email } } }',
            'variables' => [
                'input' => [
                    'name' => 'Bob Doe',
                    'email' => 'bob@example.com',
                    'password' => 'secret123',
                ],
            ],
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('users', ['email' => 'bob@example.com']);
        $response->assertJsonPath('data.createUser.user.name', 'Bob Doe');
    }
}
