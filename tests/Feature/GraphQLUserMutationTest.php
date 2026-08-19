<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
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
        $response->assertJsonPath(
            'data.createUser.user.email',
            'jane@example.com',
        );
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
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'status' => 'unactive',
        ]);
    }

    public function test_approve_package_request_creates_user_package_linked_to_email_and_plan(): void
    {
        $packageRequestsCollectionId = DB::table('collections')->insertGetId([
            'collection_name' => 'package_requests',
            'api_endpoint' => 'package_requests',
            'is_system_type' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $userPackageCollectionId = DB::table('collections')->insertGetId([
            'collection_name' => 'user_package',
            'api_endpoint' => 'user_package',
            'is_system_type' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $planId = DB::table('entries')->insertGetId([
            'collection_id' => DB::table('collections')->insertGetId([
                'collection_name' => 'plans',
                'api_endpoint' => 'plans',
                'is_system_type' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]),
            'status' => 'published',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('entry_meta')->insert([
            [
                'entry_id' => $planId,
                'meta_key' => 'plan_name',
                'meta_value' => 'Starter',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'entry_id' => $planId,
                'meta_key' => 'price',
                'meta_value' => '99',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $requestId = DB::table('entries')->insertGetId([
            'collection_id' => $packageRequestsCollectionId,
            'status' => 'published',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('entry_meta')->insert([
            [
                'entry_id' => $requestId,
                'meta_key' => 'plan_id',
                'meta_value' => (string) $planId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'entry_id' => $requestId,
                'meta_key' => 'full_name',
                'meta_value' => 'Jane Doe',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'entry_id' => $requestId,
                'meta_key' => 'email',
                'meta_value' => 'jane@example.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'entry_id' => $requestId,
                'meta_key' => 'status',
                'meta_value' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'entry_id' => $requestId,
                'meta_key' => 'duration_days',
                'meta_value' => '30',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $query = <<<'GRAPHQL'
        mutation ApprovePackageRequest($id: ID!) {
          approvePackageRequest(id: $id) {
            id
            status
          }
        }
        GRAPHQL;

        $response = $this->postJson('/graphql', [
            'query' => $query,
            'variables' => ['id' => (string) $requestId],
        ]);

        $response->assertStatus(200);

        $userPackageEntry = DB::table('entries')
            ->join(
                'entry_meta as email_meta',
                'entries.id',
                '=',
                'email_meta.entry_id',
            )
            ->where('entries.collection_id', $userPackageCollectionId)
            ->where('email_meta.meta_key', 'email')
            ->where('email_meta.meta_value', 'jane@example.com')
            ->select('entries.id')
            ->first();

        $this->assertNotNull($userPackageEntry);

        $this->assertDatabaseHas('entry_relations', [
            'parent_entry_id' => $userPackageEntry->id,
            'child_entry_id' => $planId,
            'relation_type' => 'plan',
        ]);

        $this->assertDatabaseHas('entry_meta', [
            'entry_id' => $userPackageEntry->id,
            'meta_key' => 'duration_days',
            'meta_value' => '30',
        ]);

        $this->assertDatabaseHas('entry_meta', [
            'entry_id' => $userPackageEntry->id,
            'meta_key' => 'expired_at',
        ]);
    }
}
