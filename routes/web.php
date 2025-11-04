<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// Rotas de autenticação
Route::prefix('auth')->group(function () {
    Route::get('/login', 'AuthController@login')->name('login');
    Route::post('/login', 'AuthController@authenticate');
    Route::get('/logout', 'AuthController@logout')->name('logout');
    Route::get('/forgot-password', 'AuthController@reset-password')->name('reset-password');
    Route::post('/forgot-password', 'AuthController@send-reset-password-link');
});

// Rotas Administrador
Route::prefix('admin')->group(function () {
    Route::prefix('users')->group(function () {
        Route::resource('/', 'UsersController');
    });
    Route::prefix('config')->group(function () {
        Route::resource('/', 'ConfigController');
    });
});
