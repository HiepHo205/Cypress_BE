<?php

use App\Modules\Admin\Controllers\RoleController;
use Illuminate\Support\Facades\Route;
use App\Modules\Admin\Controllers\AdminController;

Route::domain('admin.cypresshub.com')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.home');
    Route::get('/role-users', [RoleController::class, 'index'])->name('admin.roles');
});
