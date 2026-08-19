<?php

namespace App\Modules\Admin\GraphQL\Queries;

use Illuminate\Support\Facades\DB;

class ServiceRequestQuery
{
    public function list()
    {
        $collectionId = DB::table('collections')
            ->where(
                'collection_name',
                'service_requests'
            )
            ->value('id');

        $entries = DB::table('entries')
            ->where(
                'collection_id',
                $collectionId
            )
            ->latest('id')
            ->get();

        return $entries->map(
            fn($entry) =>
            $this->detail(
                null,
                ['id' => $entry->id]
            )
        );
    }

    public function detail(
        $_,
        array $args
    ) {

        $meta = DB::table('entry_meta')
            ->where(
                'entry_id',
                $args['id']
            )
            ->pluck(
                'meta_value',
                'meta_key'
            );

        return [
            'id' => $args['id'],

            'request_type' =>
                $meta['request_type'] ?? null,

            'full_name' =>
                $meta['full_name'] ?? null,

            'email' =>
                $meta['email'] ?? null,

            'phone' =>
                $meta['phone'] ?? null,

            'company_name' =>
                $meta['company_name'] ?? null,

            'address' =>
                $meta['address'] ?? null,

            'service_interest' =>
                $meta['service_interest'] ?? null,

            'message' =>
                $meta['message'] ?? null,

            'extra_data' =>
                $meta['extra_data'] ?? null,

            'status' =>
                $meta['status'] ?? null,
        ];
    }
}