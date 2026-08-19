<?php

namespace App\Modules\Admin\GraphQL\Mutations;

use Exception;
use Illuminate\Support\Facades\DB;

use App\Modules\Admin\GraphQL\Queries\LocationQuery;

class LocationMutation
{
    public function create($_, array $args)
    {
        $collectionId = DB::table('collections')
            ->where(
                'collection_name',
                'locations'
            )
            ->value('id');

        if (!$collectionId) {
            throw new Exception(
                'Location collection not found.'
            );
        }

        $entryId = DB::table('entries')
            ->insertGetId([
                'collection_id' => $collectionId,
                'status' => 'published',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        foreach ($args['input'] as $key => $value) {

            DB::table('entry_meta')
                ->insert([
                    'entry_id' => $entryId,
                    'meta_key' => $key,
                    'meta_value' => $value,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
        }

        return app(LocationQuery::class)
            ->detail(
                null,
                [
                    'id' => $entryId
                ]
            );
    }

    public function update($_, array $args)
    {
        foreach ($args['input'] as $key => $value) {

            $exists = DB::table('entry_meta')
                ->where(
                    'entry_id',
                    $args['id']
                )
                ->where(
                    'meta_key',
                    $key
                )
                ->exists();

            if ($exists) {

                DB::table('entry_meta')
                    ->where(
                        'entry_id',
                        $args['id']
                    )
                    ->where(
                        'meta_key',
                        $key
                    )
                    ->update([
                        'meta_value' => $value,
                        'updated_at' => now(),
                    ]);

            } else {

                DB::table('entry_meta')
                    ->insert([
                        'entry_id' => $args['id'],
                        'meta_key' => $key,
                        'meta_value' => $value,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
            }
        }

        return app(LocationQuery::class)
            ->detail(
                null,
                [
                    'id' => $args['id']
                ]
            );
    }

    public function delete($_, array $args)
    {
        DB::table('entry_meta')
            ->where(
                'entry_id',
                $args['id']
            )
            ->delete();

        DB::table('entries')
            ->where(
                'id',
                $args['id']
            )
            ->delete();

        return true;
    }
}