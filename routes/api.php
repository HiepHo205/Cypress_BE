<?php

use App\Modules\Api\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::domain('api.cypresshub.com')->group(function () {
    Route::get('/', function () {
        return response()->json([
            'status' => 'success',
            'message' => 'CypressHub API Client Gateway is active (.com production mode).',
            'timestamp' => now()->toIso8601String()
        ]);
    })->name('api.gateway');

    Route::post('/logout', [AuthController::class, 'logout'])
        ->middleware('auth:api')
        ->name('api.logout');
});
