<?php

use App\Http\Controllers\ReportsController;
use App\Http\Controllers\TasksController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\ClientsController;
use App\Http\Controllers\DiagnosticsController;
use App\Http\Controllers\ProposalsController;
use App\Http\Controllers\ContractsController;
use App\Http\Controllers\ActiveClientsController;
use App\Http\Controllers\LostClientsController;
use App\Http\Controllers\NotesController;

Route::get('/', function () {
    return redirect()->route('auth.login');
});

// Rotas de autenticação
Route::prefix('auth')->name('auth.')->group(function () {
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])->name('authenticate')    ;
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/forgot-password', [AuthController::class, 'resetPassword'])->name('reset.password');
    Route::post('/forgot-password', [AuthController::class, 'sendResetPasswordEmail'])->name('reset.password.send.email');
});

// Rotas Administrador
Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('/users', UsersController::class);
    Route::resource('/config', ConfigController::class);
});

Route::prefix('leads')->name('leads.')->group(function () {
    Route::resource('/clients', ClientsController::class);
    Route::resource('/diagnostics', DiagnosticsController::class);
    Route::resource('/proposals', ProposalsController::class);
    Route::resource('/contract', ContractsController::class);
    Route::resource('/actives', ActiveClientsController::class);
    Route::resource('/losts', LostClientsController::class);
    Route::resource('/notes', NotesController::class);
    Route::resource('/tasks', TasksController::class);
});

Route::prefix('dashboard')->name('dashboard.')->group(function () {
    Route::resource('/reports', ReportsController::class);
});
