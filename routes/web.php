<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProcessoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\VotacaoController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'showLogin'])->name('home');
Route::post('/', [AuthController::class, 'login'])->name('login');

Route::get('/cadastro', [AuthController::class, 'showRegister'])->name('cadastro');
Route::post('/cadastro', [AuthController::class, 'register'])->name('register');

Route::middleware(['auth', 'active'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/processos', [ProcessoController::class, 'index'])->name('processos.index');

    Route::middleware('role:admin')->group(function () {
        Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.list');
        Route::put('/usuarios/{usuario}', [UsuarioController::class, 'update'])->name('usuarios.update');
        Route::patch('/usuarios/{usuario}/aprovar', [UsuarioController::class, 'approve'])->name('usuarios.approve');
        Route::delete('/usuarios/{usuario}', [UsuarioController::class, 'destroy'])->name('usuarios.destroy');

        Route::get('/processos/criar', [ProcessoController::class, 'create'])->name('processos.create');
        Route::post('/processos', [ProcessoController::class, 'store'])->name('processos.store');
        Route::put('/processos/{processo}', [ProcessoController::class, 'update'])->name('processos.update');
        Route::delete('/processos/{processo}', [ProcessoController::class, 'destroy'])->name('processos.destroy');
    });

    Route::get('/processos/{processo}', [ProcessoController::class, 'show'])->name('processos.show');

    Route::middleware('role:membro')->group(function () {
        Route::get('/processos/{processo}/votar', [VotacaoController::class, 'create'])->name('processos.votar');
        Route::post('/processos/{processo}/votar', [VotacaoController::class, 'store'])->name('processos.votar.store');
    });

    Route::get('/perfil', function () {
        return view('perfil.edit');
    })->name('perfil');

    Route::get('/resultados', function () {
        return view('resultados.results');
    })->name('resultados');

    Route::get('/configuracoes', function () {
        return view('configuracoes');
    })->name('configuracoes');
});
