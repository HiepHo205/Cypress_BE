<?php

use App\Modules\Admin\Controllers\AdminController;
use App\Modules\Admin\Controllers\FooterController;
use App\Modules\Admin\Controllers\HeaderController;
use App\Modules\Admin\Controllers\RoleController;
use App\Modules\Admin\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.home');
    Route::get('/users', [UserController::class, 'index'])->name('admin.users');
    Route::get('/roles', [RoleController::class, 'index'])->name('admin.roles');
    Route::get('/header', [HeaderController::class, 'index'])->name('admin.header');
    Route::get('/footer', [FooterController::class, 'index'])->name('admin.header');
});
Route::get('/login', [UserController::class, 'login'])->name('login');
Route::get('/', function () {
    return redirect('/admin');
});
