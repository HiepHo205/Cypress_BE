<?php

namespace App\Modules\Admin\GraphQL\Mutations;

use Illuminate\Support\Facades\DB;
use Exception;

class PlanMutation
{
    public function create($_, array $args)
    {
        $currentUser = auth()->user();

        if (!$currentUser) {
            throw new Exception('Unauthenticated');
        }

        if (!$currentUser->hasPermission('plan.create')) {
            throw new Exception('Permission denied');
        }

        $input = $args['input'];

        $collectionId = DB::table('collections')
            ->where('collection_name', 'plans')
            ->value('id');

        $benefitCollectionId = DB::table('collections')
            ->where('collection_name', 'benefits')
            ->value('id');

        $planId = DB::table('entries')
            ->insertGetId([
                'collection_id' => $collectionId,
                'status' => 'published',
                'created_by' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        $this->updateMeta(
            $planId,
            'plan_name',
            $input['plan_name']
        );

        $this->updateMeta(
            $planId,
            'price',
            $input['price']
        );

        foreach ($input['features'] as $feature) {

            DB::table('entry_relations')
                ->insert([
                    'parent_entry_id' => $planId,
                    'child_entry_id' => $feature['id'],
                    'relation_type' => 'plan_feature',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

            foreach ($feature['benefits'] as $benefit) {

                if (empty(trim($benefit['value'] ?? ''))) {
                    throw new Exception(
                        'Benefit value cannot be empty.'
                    );
                }

                $benefitId = DB::table('entries')
                    ->insertGetId([
                        'collection_id' => $benefitCollectionId,
                        'status' => 'published',
                        'created_by' => auth()->id(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                DB::table('entry_meta')
                    ->insert([
                        'entry_id' => $benefitId,
                        'meta_key' => 'benefit_text',
                        'meta_value' => $benefit['value'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                DB::table('entry_relations')
                    ->insert([
                        [
                            'parent_entry_id' => $planId,
                            'child_entry_id' => $benefitId,
                            'relation_type' => 'plan_benefit',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ],
                        [
                            'parent_entry_id' => $feature['id'],
                            'child_entry_id' => $benefitId,
                            'relation_type' => 'feature_benefit',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ],
                    ]);
            }
        }

        return app(
            \App\Modules\Admin\GraphQL\Queries\PlanQuery::class
        )->plan(
            null,
            ['id' => $planId]
        );
    }

    public function update($_, array $args)
    {
        $currentUser = auth()->user();

        if (!$currentUser) {
            throw new Exception('Unauthenticated');
        }

        if (!$currentUser->hasPermission('plan.update')) {
            throw new Exception('Permission denied');
        }

        $planId = $args['id'];
        $input = $args['input'];

        if (empty(trim($input['plan_name'] ?? ''))) {
            throw new Exception(
                'Plan name cannot be empty.'
            );
        }

        if (empty(trim($input['price'] ?? ''))) {
            throw new Exception(
                'Price cannot be empty.'
            );
        }

        $this->updateMeta(
            $planId,
            'plan_name',
            $input['plan_name']
        );

        $this->updateMeta(
            $planId,
            'price',
            $input['price']
        );

        $currentBenefitIds = DB::table('entry_relations')
            ->where('parent_entry_id', $planId)
            ->where('relation_type', 'plan_benefit')
            ->pluck('child_entry_id')
            ->toArray();

        $submittedBenefitIds = [];

        DB::table('entry_relations')
            ->where('parent_entry_id', $planId)
            ->where('relation_type', 'plan_feature')
            ->delete();

        $benefitCollectionId = DB::table('collections')
            ->where('collection_name', 'benefits')
            ->value('id');

        foreach ($input['features'] as $feature) {

            DB::table('entry_relations')
                ->insert([
                    'parent_entry_id' => $planId,
                    'child_entry_id' => $feature['id'],
                    'relation_type' => 'plan_feature',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

            foreach ($feature['benefits'] as $benefit) {

                if (
                    empty(
                        trim(
                            $benefit['value'] ?? ''
                        )
                    )
                ) {
                    throw new Exception(
                        'Benefit value cannot be empty.'
                    );
                }

                if (!empty($benefit['id'])) {

                    $submittedBenefitIds[] =
                        (int) $benefit['id'];

                    DB::table('entry_meta')
                        ->where(
                            'entry_id',
                            $benefit['id']
                        )
                        ->where(
                            'meta_key',
                            'benefit_text'
                        )
                        ->update([
                            'meta_value' =>
                                $benefit['value'],
                            'updated_at' =>
                                now(),
                        ]);

                    continue;
                }

                $benefitId = DB::table('entries')
                    ->insertGetId([
                        'collection_id' =>
                            $benefitCollectionId,
                        'status' => 'published',
                        'created_by' =>
                            auth()->id(),
                        'created_at' =>
                            now(),
                        'updated_at' =>
                            now(),
                    ]);

                $submittedBenefitIds[] =
                    (int) $benefitId;

                DB::table('entry_meta')
                    ->insert([
                        'entry_id' =>
                            $benefitId,
                        'meta_key' =>
                            'benefit_text',
                        'meta_value' =>
                            $benefit['value'],
                        'created_at' =>
                            now(),
                        'updated_at' =>
                            now(),
                    ]);

                DB::table('entry_relations')
                    ->insert([
                        [
                            'parent_entry_id' =>
                                $planId,
                            'child_entry_id' =>
                                $benefitId,
                            'relation_type' =>
                                'plan_benefit',
                            'created_at' =>
                                now(),
                            'updated_at' =>
                                now(),
                        ],
                        [
                            'parent_entry_id' =>
                                $feature['id'],
                            'child_entry_id' =>
                                $benefitId,
                            'relation_type' =>
                                'feature_benefit',
                            'created_at' =>
                                now(),
                            'updated_at' =>
                                now(),
                        ],
                    ]);
            }
        }

        $deletedBenefitIds = array_diff(
            $currentBenefitIds,
            $submittedBenefitIds
        );

        foreach (
            $deletedBenefitIds
            as $benefitId
        ) {

            DB::table('entry_meta')
                ->where(
                    'entry_id',
                    $benefitId
                )
                ->delete();

            DB::table('entry_relations')
                ->where(
                    'child_entry_id',
                    $benefitId
                )
                ->delete();

            DB::table('entries')
                ->where(
                    'id',
                    $benefitId
                )
                ->delete();
        }

        return app(
            \App\Modules\Admin\GraphQL\Queries\PlanQuery::class
        )->plan(
            null,
            ['id' => $planId]
        );
    }

    public function delete($_, array $args): bool
    {
        $currentUser = auth()->user();

        if (!$currentUser) {
            throw new Exception('Unauthenticated');
        }

        if (!$currentUser->hasPermission('plan.delete')) {
            throw new Exception('Permission denied');
        }

        DB::table('entry_meta')
            ->where('entry_id', $args['id'])
            ->delete();

        DB::table('entry_relations')
            ->where('parent_entry_id', $args['id'])
            ->orWhere('child_entry_id', $args['id'])
            ->delete();

        DB::table('entries')
            ->where('id', $args['id'])
            ->delete();

        return true;
    }

    private function updateMeta(
        int $entryId,
        string $key,
        string $value
    ): void {
        DB::table('entry_meta')
            ->updateOrInsert(
                [
                    'entry_id' => $entryId,
                    'meta_key' => $key,
                ],
                [
                    'meta_value' => $value,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
    }
}