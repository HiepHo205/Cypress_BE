<?php

use App\Modules\Admin\Controllers\AdminController;
use App\Modules\Admin\Controllers\RoleController;
use App\Modules\Admin\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.home');
    Route::get('/users', [UserController::class, 'index'])->name('admin.users');
    Route::get('/roles', [RoleController::class, 'index'])->name('admin.roles');
    Route::get('/{any}', [AdminController::class, 'index'])
    ->where('any', '.*');
});
Route::get('/login', [UserController::class, 'login'])->name('login');
Route::get('/', function () {
    return redirect('/admin');
});
