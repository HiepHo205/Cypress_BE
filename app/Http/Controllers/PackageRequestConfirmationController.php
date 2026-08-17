<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Modules\Admin\GraphQL\Mutations\PackageRequestMutation;
use App\Modules\Admin\GraphQL\Queries\PackageRequestQuery;

class PackageRequestConfirmationController extends Controller
{
    public function confirm($token)
    {
        try {
            $tokenMeta = DB::table('entry_meta')
                ->where('meta_key', 'confirmation_token')
                ->where('meta_value', $token)
                ->first();

            if (!$tokenMeta) {
                return view('package-request-confirmation-result', [
                    'success' => false,
                    'message' => 'Invalid or expired confirmation link.',
                    'title' => 'Confirmation Failed',
                ]);
            }

            $requestMeta = DB::table('entry_meta')
                ->where('entry_id', $tokenMeta->entry_id)
                ->where('meta_key', 'status')
                ->first();

            if (
                $requestMeta &&
                $requestMeta->meta_value !== 'waiting_confirmation'
            ) {
                return view('package-request-confirmation-result', [
                    'success' => false,
                    'message' => 'This request has already been processed.',
                    'title' => 'Already Processed',
                ]);
            }

            // Call the mutation
            $mutation = app(PackageRequestMutation::class);
            $result = $mutation->confirm(null, ['token' => $token]);

            return view('package-request-confirmation-result', [
                'success' => true,
                'message' =>
                    'Your package change has been confirmed! The admin will review your request shortly.',
                'title' => 'Confirmed!',
            ]);
        } catch (\Exception $e) {
            return view('package-request-confirmation-result', [
                'success' => false,
                'message' => $e->getMessage(),
                'title' => 'Error',
            ]);
        }
    }

    public function keepCurrent($token)
    {
        try {
            $tokenMeta = DB::table('entry_meta')
                ->where('meta_key', 'confirmation_token')
                ->where('meta_value', $token)
                ->first();

            if (!$tokenMeta) {
                return view('package-request-confirmation-result', [
                    'success' => false,
                    'message' => 'Invalid or expired confirmation link.',
                    'title' => 'Request Failed',
                ]);
            }

            $requestMeta = DB::table('entry_meta')
                ->where('entry_id', $tokenMeta->entry_id)
                ->where('meta_key', 'status')
                ->first();

            if (
                $requestMeta &&
                $requestMeta->meta_value !== 'waiting_confirmation'
            ) {
                return view('package-request-confirmation-result', [
                    'success' => false,
                    'message' => 'This request has already been processed.',
                    'title' => 'Already Processed',
                ]);
            }

            // Call the mutation
            $mutation = app(PackageRequestMutation::class);
            $result = $mutation->keepCurrentPackage(null, ['token' => $token]);

            return view('package-request-confirmation-result', [
                'success' => true,
                'message' =>
                    'Your current package will remain active. The package change request has been cancelled.',
                'title' => 'Cancelled!',
            ]);
        } catch (\Exception $e) {
            return view('package-request-confirmation-result', [
                'success' => false,
                'message' => $e->getMessage(),
                'title' => 'Error',
            ]);
        }
    }
}
