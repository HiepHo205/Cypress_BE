<?php

use App\Modules\Admin\Controllers\AdminController;
use App\Modules\Admin\Controllers\FooterController;
use App\Modules\Admin\Controllers\HeaderController;
use App\Modules\Admin\Controllers\RoleController;
use App\Modules\Admin\Controllers\UserController;
use App\Http\Controllers\PackageRequestConfirmationController;
use Illuminate\Support\Facades\Route;

// Public routes for package request confirmation
Route::get('/package-request/confirm/{token}', [
    PackageRequestConfirmationController::class,
    'confirm',
])->name('package-request.confirm');

Route::get('/package-request/keep-current/{token}', [
    PackageRequestConfirmationController::class,
    'keepCurrent',
])->name('package-request.keep-current');

Route::prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.home');
    Route::get('/users', [UserController::class, 'index'])->name('admin.users');
    Route::get('/roles', [RoleController::class, 'index'])->name('admin.roles');
    Route::get('/{any}', [AdminController::class, 'index'])->where('any', '.*');
    Route::get('/header', [HeaderController::class, 'index'])->name(
        'admin.header',
    );
    Route::get('/footer', [FooterController::class, 'index'])->name(
        'admin.header',
    );
});
Route::get('/login', [UserController::class, 'login'])->name('login');
Route::get('/', function () {
    return redirect('/admin');
});
