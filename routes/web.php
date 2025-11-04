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
    Route::resource('/users', 'UsersController')->name('users');
    Route::resource('/config', 'ConfigController')->name('config');
});

Route::prefix('leads')->group(function () {
    Route::resource('/clients', 'ClientsController')->name('clients');
    Route::resource('/diagnostics', 'DiagnosticsController')->name('diagnostics');
    Route::resource('/proposals', 'ProposalsController')->name('proposals');
    Route::resource('/contract', 'ContractsController')->name('contracts');
    Route::resource('/actives', 'ActiveClientsController')->name('active-clients');
    Route::resource('/losts', 'LostClientsController')->name('lost-clients');
    Route::resource('/notes', 'NotesController')->name('notes');
    Route::resource('/tasks', 'ProposalsController')->name('tasks');
});

Route::prefix('dashboard')->group(function () {
    Route::resource('/reports', 'LeadsController')->name('reports');
    Route::resource('/notes', 'ProposalsController')->name('notes');
});
