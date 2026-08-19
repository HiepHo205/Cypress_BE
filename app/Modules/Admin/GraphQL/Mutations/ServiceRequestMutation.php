<?php

namespace App\Modules\Admin\GraphQL\Mutations;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

use App\Mail\ServiceRequestReceivedMail;
use App\Mail\ServiceRequestApprovedMail;
use App\Mail\ServiceRequestRejectedMail;

use App\Modules\Admin\GraphQL\Validators\ServiceRequestValidator;
use App\Modules\Admin\GraphQL\Queries\ServiceRequestQuery;

class ServiceRequestMutation
{
    public function create($_, array $args)
    {
        $input = $args['input'];

        ServiceRequestValidator::validate($input);

        $collectionId = DB::table('collections')
            ->where('collection_name', 'service_requests')
            ->value('id');

        if (!$collectionId) {
            throw new Exception(
                'Service request collection not found.'
            );
        }

        $entryId = DB::table('entries')
            ->insertGetId([
                'collection_id' => $collectionId,
                'status'        => 'published',
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

        $fields = [
            'request_type',
            'full_name',
            'email',
            'phone',
            'company_name',
            'address',
            'service_interest',
            'message',
            'extra_data',
        ];

        foreach ($fields as $field) {
            DB::table('entry_meta')->insert([
                'entry_id'   => $entryId,
                'meta_key'   => $field,
                'meta_value' => $input[$field] ?? '',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('entry_meta')->insert([
            'entry_id'   => $entryId,
            'meta_key'   => 'status',
            'meta_value' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Mail::to($input['email'])
            ->send(
                new ServiceRequestReceivedMail([
                    'full_name'    => $input['full_name'],
                    'request_type' => $input['request_type'],
                ])
            );

        return app(ServiceRequestQuery::class)
            ->detail(
                null,
                ['id' => $entryId]
            );
    }

    public function approve($_, array $args)
    {
        $request = app(ServiceRequestQuery::class)
            ->detail(
                null,
                ['id' => $args['id']]
            );

        if (!$request) {
            throw new Exception('Request not found.');
        }

        if ($request['status'] !== 'pending') {
            throw new Exception(
                'Request already processed.'
            );
        }

        DB::table('entry_meta')
            ->where('entry_id', $args['id'])
            ->where('meta_key', 'status')
            ->update([
                'meta_value' => 'approved',
                'updated_at' => now(),
            ]);

        Mail::to($request['email'])
            ->send(
                new ServiceRequestApprovedMail(
                    $request
                )
            );

        return app(ServiceRequestQuery::class)
            ->detail(
                null,
                ['id' => $args['id']]
            );
    }

    public function reject($_, array $args)
    {
        $request = app(ServiceRequestQuery::class)
            ->detail(
                null,
                ['id' => $args['id']]
            );

        if (!$request) {
            throw new Exception('Request not found.');
        }

        if ($request['status'] !== 'pending') {
            throw new Exception(
                'Request already processed.'
            );
        }

        DB::table('entry_meta')
            ->where('entry_id', $args['id'])
            ->where('meta_key', 'status')
            ->update([
                'meta_value' => 'rejected',
                'updated_at' => now(),
            ]);

        Mail::to($request['email'])
            ->send(
                new ServiceRequestRejectedMail(
                    $request
                )
            );

        return app(ServiceRequestQuery::class)
            ->detail(
                null,
                ['id' => $args['id']]
            );
    }
}
