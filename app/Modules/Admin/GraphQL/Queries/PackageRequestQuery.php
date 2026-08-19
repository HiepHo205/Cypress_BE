<?php

namespace App\Modules\Admin\GraphQL\Queries;

use Illuminate\Support\Facades\DB;

class PackageRequestQuery
{
    public function list()
    {
        $collectionId = DB::table('collections')
            ->where('collection_name', 'package_requests')
            ->value('id');

        if (!$collectionId) {
            return [];
        }

        $entries = DB::table('entries')
            ->where('collection_id', $collectionId)
            ->latest('id')
            ->get();

        return $entries->map(fn($entry) => $this->formatRequest($entry->id));
    }

    public function detail($_, array $args)
    {
        return $this->formatRequest($args['id']);
    }

    private function formatRequest(int $entryId): array
    {
        $meta = DB::table('entry_meta')
            ->where('entry_id', $entryId)
            ->pluck('meta_value', 'meta_key');

        $planId = $meta['plan_id'] ?? null;
        $planName = null;

        if ($planId) {
            $planName = DB::table('entry_meta')
                ->where('entry_id', (int) $planId)
                ->where('meta_key', 'plan_name')
                ->value('meta_value');
        }

        return [
            'id' => $entryId,

            'plan_id' => $planId,

            'plan_name' => $planName ?? ($meta['plan_name'] ?? null),

            'duration_days' =>
                $meta['duration_days'] !== null
                    ? (int) $meta['duration_days']
                    : null,

            'full_name' => $meta['full_name'] ?? null,

            'email' => $meta['email'] ?? null,

            'phone' => $meta['phone'] ?? null,

            'company_name' => $meta['company_name'] ?? null,

            'request_data' => $meta['request_data'] ?? null,

            'started_at' => $meta['started_at'] ?? null,

            'expired_at' => $meta['expired_at'] ?? null,

            'status' => $meta['status'] ?? 'pending',
        ];
    }
}
