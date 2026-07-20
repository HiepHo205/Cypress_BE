<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/admin/{any?}', function () {
    return view('app');
})->where('any', '.*')->name('admin.spa');
