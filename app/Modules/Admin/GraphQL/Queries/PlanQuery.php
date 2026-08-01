<?php

namespace App\Modules\Admin\GraphQL\Queries;

use Illuminate\Support\Facades\DB;

class PlanQuery
{
    public function plans()
    {
        $collectionId = DB::table('collections')
            ->where('collection_name', 'plans')
            ->value('id');

        $entries = DB::table('entries')
            ->where('collection_id', $collectionId)
            ->get();

        return $entries->map(function ($entry) {

            $meta = DB::table('entry_meta')
                ->where('entry_id', $entry->id)
                ->pluck('meta_value', 'meta_key');

            return [
                'id' => $entry->id,
                'plan_name' => $meta['plan_name'] ?? '',
                'price' => $meta['price'] ?? '',
            ];
        });
    }

    public function plan($_, array $args)
    {
        $planId = $args['id'];

        $meta = DB::table('entry_meta')
            ->where('entry_id', $planId)
            ->pluck('meta_value', 'meta_key');

        $featureRelations = DB::table('entry_relations')
            ->where('parent_entry_id', $planId)
            ->where('relation_type', 'plan_feature')
            ->get();

        $features = [];

        foreach ($featureRelations as $relation) {

            $featureId = $relation->child_entry_id;

            $featureMeta = DB::table('entry_meta')
                ->where('entry_id', $featureId)
                ->pluck('meta_value', 'meta_key');

            $benefitRelations = DB::table('entry_relations')
                ->where('parent_entry_id', $featureId)
                ->where('relation_type', 'feature_benefit')
                ->get();

            $benefits = [];

            foreach ($benefitRelations as $benefitRelation) {

                $benefitId = $benefitRelation->child_entry_id;

                $belongsToPlan = DB::table('entry_relations')
                    ->where('parent_entry_id', $planId)
                    ->where('child_entry_id', $benefitId)
                    ->where('relation_type', 'plan_benefit')
                    ->exists();

                if (!$belongsToPlan) {
                    continue;
                }

                $benefitMeta = DB::table('entry_meta')
                    ->where('entry_id', $benefitId)
                    ->pluck('meta_value', 'meta_key');

                $benefits[] = [
                    'id' => $benefitId,
                    'value' => $benefitMeta['benefit_text'] ?? '',
                ];
            }

            $features[] = [
                'id' => $featureId,
                'name' => $featureMeta['name'] ?? '',
                'group' => $featureMeta['group'] ?? '',
                'benefits' => $benefits,
            ];
        }

        return [
            'id' => $planId,
            'plan_name' => $meta['plan_name'] ?? '',
            'price' => $meta['price'] ?? '',
            'features' => $features,
        ];
    }

    public function features()
    {
        $collectionId = DB::table('collections')
            ->where('collection_name', 'features')
            ->value('id');

        $entries = DB::table('entries')
            ->where('collection_id', $collectionId)
            ->get();

        return $entries->map(function ($entry) {

            $meta = DB::table('entry_meta')
                ->where('entry_id', $entry->id)
                ->pluck('meta_value', 'meta_key');

            return [
                'id' => $entry->id,
                'name' => $meta['name'] ?? '',
                'group' => $meta['group'] ?? '',
            ];
        });
    }
}