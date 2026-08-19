<?php

namespace App\Modules\Admin\GraphQL\Queries;

use Illuminate\Support\Facades\DB;

class LocationQuery
{
    public function list()
    {
        $collectionId = DB::table('collections')
            ->where('collection_name', 'locations')
            ->value('id');

        if (!$collectionId) {
            return [];
        }

        $entries = DB::table('entries')
            ->where('collection_id', $collectionId)
            ->where('status', 'published')
            ->latest('id')
            ->get();

        return $entries->map(
            fn ($entry) => $this->mapLocation($entry->id)
        );
    }

    public function detail($_, array $args)
    {
        $exists = DB::table('entries')
            ->where('id', $args['id'])
            ->exists();

        if (! $exists) {
            return null;
        }

        return $this->mapLocation(
            $args['id']
        );
    }

    private function mapLocation(int $entryId): array
    {
        $meta = DB::table('entry_meta')
            ->where('entry_id', $entryId)
            ->pluck(
                'meta_value',
                'meta_key'
            );

        return [
            'id' => $entryId,
            'name' => $meta['name'] ?? '',
            'address' => $meta['address'] ?? '',
            'latitude' => (float) ($meta['latitude'] ?? 0),
            'longitude' => (float) ($meta['longitude'] ?? 0),
            'status' => $meta['status'] ?? 'active',
        ];
    }
}