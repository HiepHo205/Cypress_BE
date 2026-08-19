<?php

namespace App\Modules\Admin\GraphQL\Queries;

use Illuminate\Support\Facades\DB;

class UserPackageQuery
{
    public function list(): array
    {
        $collectionId = $this->resolveCollectionId([
            'user_package',
            'user_packages',
        ]);

        if (!$collectionId) {
            return [];
        }

        $entries = DB::table('entries')
            ->where(
                'collection_id',
                $collectionId
            )
            ->orderByDesc('id')
            ->get();

        return $entries
            ->map(
                fn($entry) => $this->transformEntry(
                    $entry->id
                )
            )
            ->filter()
            ->values()
            ->toArray();
    }

    public function detail(
        $_,
        array $args
    ): ?array {

        return $this->transformEntry(
            (int) $args['id']
        );
    }

    private function transformEntry(
        int $entryId
    ): ?array {

        $entry = DB::table('entries')
            ->where(
                'id',
                $entryId
            )
            ->first();

        if (!$entry) {
            return null;
        }

        $meta = DB::table('entry_meta')
            ->where(
                'entry_id',
                $entryId
            )
            ->pluck(
                'meta_value',
                'meta_key'
            );

        $planId = (int) (
            $meta['plan_id']
            ?? 0
        );

        $planName = null;

        if ($planId) {

            $planName = DB::table('entry_meta')
                ->where(
                    'entry_id',
                    $planId
                )
                ->where(
                    'meta_key',
                    'plan_name'
                )
                ->value(
                    'meta_value'
                );
        }

        $expiredAt =
            $meta['expired_at']
            ?? null;

        $status =
            $meta['status']
            ?? 'active';

        if (
            $expiredAt &&
            strtotime($expiredAt) < time()
        ) {
            $status = 'expired';
        }

        return [
            'id'            => $entryId,
            'email'         => $meta['email'] ?? null,
            'plan_id'       => $planId,
            'plan_name'     => $planName,
            'duration_days' => isset(
                $meta['duration_days']
            )
                ? (int) $meta['duration_days']
                : null,
            'started_at'    => $meta['started_at'] ?? null,
            'expired_at'    => $expiredAt,
            'status'        => $status,
        ];
    }

    private function resolveCollectionId(
        array $names
    ): ?int {

        return DB::table(
            'collections'
        )
            ->whereIn(
                'collection_name',
                $names
            )
            ->value(
                'id'
            );
    }
}