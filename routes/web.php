<?php

use App\Modules\Admin\Controllers\AdminController;
use App\Modules\Admin\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.home');
    Route::get('/users', [UserController::class, 'index'])->name('admin.users');
});

Route::get('/', function () {
    return redirect('/admin');
});