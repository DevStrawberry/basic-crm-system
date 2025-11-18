<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LeadsController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\TasksController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\SettingsController;
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
    Route::get('login', [AuthController::class, 'index'])->name('login');
    Route::post('login', [AuthController::class, 'authenticate'])->name('authenticate')    ;
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('forgot-password', [AuthController::class, 'resetPassword'])->name('reset.password');
    Route::post('forgot-password', [AuthController::class, 'sendResetPasswordEmail'])->name('reset.password.send.email');
    Route::get('change-password', [AuthController::class, 'showChangePasswordForm'])->name('change.password');
    Route::post('change-password', [AuthController::class, 'updatePassword'])->name('change.password.update');
});

// Rotas Administrador
Route::middleware(['auth', 'force.password.change', 'role:administrador'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::resource('dashboard', DashboardController::class);
        Route::resource('users', UsersController::class);
        Route::resource('settings', SettingsController::class);
});


Route::middleware(['auth', 'force.password.change', 'role:gestor,assessor'])->group(function () {
    // Rotas dashboard, páginas iniciais depois do login
    Route::resource('dashboard', DashboardController::class);

    // Rotas de relatórios
    Route::resource('reports', ReportsController::class);

    // Rota para cadastro de clientes
    Route::resource('clients', ClientsController::class);

    // Rotas para cadastro de leads
    Route::resource('leads', LeadsController::class);

    // Rotas para tarefas
    Route::resource('tasks', TasksController::class);

    // Rotas para gerenciar leads específicas
    Route::prefix('leads/{lead_id}')->name('leads.')->group(function () {
        Route::resource('diagnostics', DiagnosticsController::class);
        Route::resource('proposals', ProposalsController::class);
        Route::resource('contract', ContractsController::class);
        Route::resource('actives', ActiveClientsController::class);
        Route::resource('losts', LostClientsController::class);
        Route::resource('notes', NotesController::class);
        
        // Rotas específicas de contratos
        Route::post('contract/{contract}/interact', [ContractsController::class, 'interact'])->name('contract.interact');
        Route::post('contract/{contract}/assign', [ContractsController::class, 'assign'])->name('contract.assign');
        Route::post('contract/{contract}/sign', [ContractsController::class, 'sign'])->name('contract.sign');

        //rotas especificas de propostas
        Route::get('proposals/{proposal}/pdf', [ProposalsController::class, 'generatePdf'])->name('proposals.pdf');
        Route::post('proposals/{proposal}/email', [ProposalsController::class, 'sendEmail'])->name('proposals.email');
        Route::post('proposals/{proposal}/approve', [ProposalsController::class, 'approve'])->name('proposals.approve');
        Route::post('proposals/{proposal}/reject', [ProposalsController::class, 'reject'])->name('proposals.reject');
        
    });

    // Rotas de contratos (fora do contexto de lead)
    Route::prefix('contracts')->name('contracts.')->group(function () {
        Route::get('my-list', [ContractsController::class, 'myList'])->name('my-list');
        Route::get('all', [ContractsController::class, 'all'])->name('all');
    });
});






