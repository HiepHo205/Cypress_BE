<?php

namespace App\Modules\Admin\GraphQL\Mutations;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

use App\Mail\PackageRequestReceivedMail;
use App\Mail\PackageRequestApprovedMail;
use App\Mail\PackageRequestRejectedMail;
use App\Mail\PackageRequestConfirmationMail;

use App\Modules\Admin\GraphQL\Validators\PackageRequestValidator;
use App\Modules\Admin\GraphQL\Queries\PackageRequestQuery;

class PackageRequestMutation
{
    public function create($_, array $args)
    {
        $input = $args['input'];

        PackageRequestValidator::validate($input);

        $collectionId = DB::table('collections')
            ->where('collection_name', 'package_requests')
            ->value('id');

        if (!$collectionId) {
            throw new Exception('Package request collection not found.');
        }

        $activePackage = $this->findActivePackageByEmail($input['email']);

        $status = $activePackage ? 'waiting_confirmation' : 'pending';

        $confirmationToken = $activePackage ? (string) str()->uuid() : null;

        $entryId = DB::table('entries')->insertGetId([
            'collection_id' => $collectionId,
            'status' => 'published',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $fields = [
            'plan_id',
            'duration_days',

            'full_name',
            'email',
            'phone',

            'company_name',

            'request_data',

            'confirmation_token',
        ];

        foreach ($fields as $field) {
            DB::table('entry_meta')->insert([
                'entry_id' => $entryId,
                'meta_key' => $field,
                'meta_value' => match ($field) {
                    'confirmation_token' => $confirmationToken,

                    default => $input[$field] ?? '',
                },
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('entry_meta')->insert([
            'entry_id' => $entryId,
            'meta_key' => 'status',
            'meta_value' => $status,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if (!$activePackage) {
            Mail::to($input['email'])->send(
                new PackageRequestReceivedMail($input),
            );

            return [
                'success' => true,
                'requires_confirmation' => false,
                'message' => 'Your package request has been received.',
            ];
        }

        Mail::to($input['email'])->send(
            new PackageRequestConfirmationMail([
                'full_name' => $input['full_name'],
                'current_package' => $activePackage['plan_name'] ?? '',
                'expired_at' => $activePackage['expired_at'] ?? '',
                'requested_plan_id' => $input['plan_id'],
                'requested_duration_days' => $input['duration_days'],
                'confirm_url' =>
                    config('app.url') .
                    '/package-request/confirm/' .
                    $confirmationToken,
                'keep_current_url' =>
                    config('app.url') .
                    '/package-request/keep-current/' .
                    $confirmationToken,
            ]),
        );

        return [
            'success' => true,
            'requires_confirmation' => true,
            'message' =>
                'A confirmation email has been sent to confirm the package change.',
        ];
    }

    public function confirm($_, array $args)
    {
        $tokenMeta = DB::table('entry_meta')
            ->where('meta_key', 'confirmation_token')
            ->where('meta_value', $args['token'])
            ->first();

        if (!$tokenMeta) {
            throw new Exception('Invalid confirmation token.');
        }

        // Update status from waiting_confirmation to pending
        $updated = DB::table('entry_meta')
            ->where('entry_id', $tokenMeta->entry_id)
            ->where('meta_key', 'status')
            ->where('meta_value', 'waiting_confirmation')
            ->update([
                'meta_value' => 'pending',
                'updated_at' => now(),
            ]);

        if ($updated === 0) {
            throw new Exception(
                'Request has already been processed or status is invalid.',
            );
        }

        // Clear the confirmation token after use
        DB::table('entry_meta')
            ->where('entry_id', $tokenMeta->entry_id)
            ->where('meta_key', 'confirmation_token')
            ->update([
                'meta_value' => null,
                'updated_at' => now(),
            ]);

        DB::table('entry_meta')->insert([
            'entry_id' => $tokenMeta->entry_id,
            'meta_key' => 'confirmed_at',
            'meta_value' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return app(PackageRequestQuery::class)->detail(null, [
            'id' => $tokenMeta->entry_id,
        ]);
    }

    public function keepCurrentPackage($_, array $args)
    {
        $tokenMeta = DB::table('entry_meta')
            ->where('meta_key', 'confirmation_token')
            ->where('meta_value', $args['token'])
            ->first();

        if (!$tokenMeta) {
            throw new Exception('Invalid confirmation token.');
        }

        // Update status from waiting_confirmation to rejected
        $updated = DB::table('entry_meta')
            ->where('entry_id', $tokenMeta->entry_id)
            ->where('meta_key', 'status')
            ->where('meta_value', 'waiting_confirmation')
            ->update([
                'meta_value' => 'rejected',
                'updated_at' => now(),
            ]);

        if ($updated === 0) {
            throw new Exception(
                'Request has already been processed or status is invalid.',
            );
        }

        // Clear the confirmation token after use
        DB::table('entry_meta')
            ->where('entry_id', $tokenMeta->entry_id)
            ->where('meta_key', 'confirmation_token')
            ->update([
                'meta_value' => null,
                'updated_at' => now(),
            ]);

        $request = app(PackageRequestQuery::class)->detail(null, [
            'id' => $tokenMeta->entry_id,
        ]);

        DB::table('entry_meta')->insert([
            'entry_id' => $tokenMeta->entry_id,
            'meta_key' => 'rejected_at',
            'meta_value' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Mail::to($request['email'])->send(
            new PackageRequestRejectedMail($request),
        );

        return app(PackageRequestQuery::class)->detail(null, [
            'id' => $tokenMeta->entry_id,
        ]);
    }

    public function approve($_, array $args)
    {
        $request = app(PackageRequestQuery::class)->detail(null, [
            'id' => $args['id'],
        ]);

        $this->deactivateOldPackages($request['email']);

        $this->createUserPackage($request);

        DB::table('entry_meta')
            ->where('entry_id', $args['id'])
            ->where('meta_key', 'status')
            ->update([
                'meta_value' => 'approved',
                'updated_at' => now(),
            ]);

        Mail::to($request['email'])->send(
            new PackageRequestApprovedMail($request),
        );

        return app(PackageRequestQuery::class)->detail(null, [
            'id' => $args['id'],
        ]);
    }

    public function reject($_, array $args)
    {
        $request = app(PackageRequestQuery::class)->detail(null, [
            'id' => $args['id'],
        ]);

        DB::table('entry_meta')
            ->where('entry_id', $args['id'])
            ->where('meta_key', 'status')
            ->update([
                'meta_value' => 'rejected',
                'updated_at' => now(),
            ]);

        Mail::to($request['email'])->send(
            new PackageRequestRejectedMail($request),
        );

        return app(PackageRequestQuery::class)->detail(null, [
            'id' => $args['id'],
        ]);
    }

    private function deactivateOldPackages(string $email): void
    {
        $collectionId = $this->resolveCollectionId([
            'user_package',
            'user_packages',
        ]);

        if (!$collectionId) {
            return;
        }

        $entryIds = DB::table('entries')
            ->join(
                'entry_meta as email_meta',
                'entries.id',
                '=',
                'email_meta.entry_id',
            )
            ->where('entries.collection_id', $collectionId)
            ->where('email_meta.meta_key', 'email')
            ->where('email_meta.meta_value', $email)
            ->pluck('entries.id');

        DB::table('entry_meta')
            ->whereIn('entry_id', $entryIds)
            ->where('meta_key', 'status')
            ->update([
                'meta_value' => 'expired',
                'updated_at' => now(),
            ]);

        DB::table('entry_meta')
            ->whereIn('entry_id', $entryIds)
            ->where('meta_key', 'expired_at')
            ->whereNull('meta_value')
            ->update([
                'meta_value' => now(),
                'updated_at' => now(),
            ]);
    }

    private function createUserPackage(array $request): void
    {
        $collectionId = $this->resolveCollectionId([
            'user_package',
            'user_packages',
        ]);

        if (!$collectionId) {
            throw new Exception('User package collection not found.');
        }

        $planId = (int) ($request['plan_id'] ?? 0);
        $durationDays = (int) ($request['duration_days'] ?? 30);

        if (!$planId) {
            throw new Exception(
                'Plan is required for approved package request.',
            );
        }

        $durationDays = in_array($durationDays, [30, 60, 90], true)
            ? $durationDays
            : 30;

        $exists = DB::table('entries')
            ->join(
                'entry_meta as email_meta',
                'entries.id',
                '=',
                'email_meta.entry_id',
            )
            ->join(
                'entry_relations',
                'entries.id',
                '=',
                'entry_relations.parent_entry_id',
            )
            ->where('entries.collection_id', $collectionId)
            ->where('email_meta.meta_key', 'email')
            ->where('email_meta.meta_value', $request['email'])
            ->where('entry_relations.child_entry_id', $planId)
            ->where('entry_relations.relation_type', 'plan')
            ->exists();

        if ($exists) {
            throw new Exception('User already owns this package.');
        }

        $userPackageId = DB::table('entries')->insertGetId([
            'collection_id' => $collectionId,
            'status' => 'published',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $startedAt = now();
        $expiredAt = $startedAt->copy()->addDays($durationDays);

        $meta = [
            'email' => $request['email'],
            'plan_id' => $planId,
            'duration_days' => $durationDays,
            'status' => 'active',
            'started_at' => $startedAt,
            'expired_at' => $expiredAt,
        ];

        foreach ($meta as $key => $value) {
            DB::table('entry_meta')->insert([
                'entry_id' => $userPackageId,
                'meta_key' => $key,
                'meta_value' => $value,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('entry_relations')->insert([
            'parent_entry_id' => $userPackageId,
            'child_entry_id' => $planId,
            'relation_type' => 'plan',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function findActivePackageByEmail(string $email): ?array
    {
        $collectionId = $this->resolveCollectionId([
            'user_package',
            'user_packages',
        ]);

        if (!$collectionId) {
            return null;
        }

        $entries = DB::table('entries')
            ->join(
                'entry_meta as email_meta',
                'entries.id',
                '=',
                'email_meta.entry_id',
            )
            ->where('entries.collection_id', $collectionId)
            ->where('email_meta.meta_key', 'email')
            ->where('email_meta.meta_value', $email)
            ->select('entries.id')
            ->get();

        foreach ($entries as $entry) {
            $packageMeta = DB::table('entry_meta')
                ->where('entry_id', $entry->id)
                ->pluck('meta_value', 'meta_key')
                ->toArray();

            $status = $packageMeta['status'] ?? null;
            $expiredAt = $packageMeta['expired_at'] ?? null;

            if ($status === 'active' && $expiredAt && now()->lt($expiredAt)) {
                $userPackageId = $entry->id;
                $planRelation = DB::table('entry_relations')
                    ->where('parent_entry_id', $userPackageId)
                    ->where('relation_type', 'plan')
                    ->first();

                $planId = $planRelation
                    ? (int) $planRelation->child_entry_id
                    : null;
                $planMeta = $planId
                    ? DB::table('entry_meta')
                        ->where('entry_id', $planId)
                        ->pluck('meta_value', 'meta_key')
                        ->toArray()
                    : [];

                return [
                    'id' => $userPackageId,
                    'plan_id' => $planId,
                    'plan_name' => $planMeta['plan_name'] ?? null,
                    'expired_at' => $packageMeta['expired_at'] ?? null,
                ];
            }

            if ($status === 'active' && $expiredAt && now()->gte($expiredAt)) {
                DB::table('entry_meta')
                    ->where('entry_id', $entry->id)
                    ->where('meta_key', 'status')
                    ->update([
                        'meta_value' => 'expired',
                        'updated_at' => now(),
                    ]);
            }
        }

        return null;
    }

    private function resolveCollectionId(array $possibleNames): ?int
    {
        foreach ($possibleNames as $name) {
            $collectionId = DB::table('collections')
                ->where('collection_name', $name)
                ->value('id');

            if ($collectionId) {
                return (int) $collectionId;
            }
        }

        return null;
    }
}
