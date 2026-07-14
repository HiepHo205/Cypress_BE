<?php

use Illuminate\Support\Facades\Route;

Route::domain('admin.cypresshub.com')->group(function () {
    Route::get('{any}', [\App\Modules\Admin\Controllers\AdminController::class, 'index'])
        ->where('any', '.*')
        ->name('admin.gateway');
});