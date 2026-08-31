<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\ProcessoController;
use App\Http\Controllers\UsuarioMembroController;
use App\Http\Controllers\VotacaoController;
use Illuminate\Support\Facades\Route;

/*
Route::view('/', 'auth.login')->name('login');

Route::view('/cadastro', 'auth.register')->name('register');
*/

Route::view('/recuperar-senha', 'home.forgot-password')->name('forgot-password');

Route::get('/', [AuthController::class, 'showLogin'])->name('home');
Route::post('/', [AuthController::class, 'login'])->name('login');

Route::get('/cadastro', [AuthController::class, 'showRegister'])->name('cadastro');
Route::post('/cadastro', [AuthController::class, 'register'])->name('register');

Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/processos', [ProcessoController::class, 'index'])->name('processos.index');

    Route::get('/processos/devolvidos', [ProcessoController::class, 'devolvidos'])->name('processos.devolvidos');

    Route::get('/processos/arquivados', [ProcessoController::class, 'arquivados'])->name('processos.arquivados');

    Route::get('/processos/meus', [ProcessoController::class, 'meus'])->name('processos.meus');

    Route::get('/processos/publicos', [ProcessoController::class, 'publicos'])->name('processos.publicos');

    Route::get('/usuarios', [UsuarioMembroController::class, 'index'])->name('usuarios.list');
    Route::put('/usuarios/{usuario}', [UsuarioMembroController::class, 'update'])->name('usuarios.update');
    Route::patch('/usuarios/{usuario}/aprovar', [UsuarioMembroController::class, 'approve'])->name('usuarios.approve');
    Route::delete('/usuarios/{usuario}', [UsuarioMembroController::class, 'destroy'])->name('usuarios.destroy');

    Route::get('/processos/criar', [ProcessoController::class, 'create'])->name('processos.create');
    Route::post('/processos', [ProcessoController::class, 'store'])->name('processos.store');
    Route::put('/processos/{processo}', [ProcessoController::class, 'update'])->name('processos.update');
    Route::delete('/processos/{processo}', [ProcessoController::class, 'destroy'])->name('processos.destroy');

    Route::get('/processos/{processo}', [ProcessoController::class, 'show'])->name('processos.show');

    Route::get('/processos/{processo}/aceitar', [ProcessoController::class, 'aceitar'])->name('processos.aceitar');
    Route::put('/processos/{processo}/aceitar', [ProcessoController::class, 'processarAceitar'])->name('processos.aceitar.processar');

    Route::put('/processos/{processo}/admin-acao', [ProcessoController::class, 'acaoAdmin'])->name('processos.admin-acao');

    Route::put('/processos/{processo}/devolver-secretario', [ProcessoController::class, 'devolverSecretario'])->name('processos.devolver-secretario');

    Route::put('/processos/{processo}/iniciar-votacao', [ProcessoController::class, 'iniciarVotacao'])->name('processos.iniciar-votacao');

    Route::get('/processos/{processo}/votar', [VotacaoController::class, 'create'])->name('processos.votar');
    Route::post('/processos/{processo}/votar', [VotacaoController::class, 'store'])->name('processos.votar.store');

    Route::get('/votacoes/disponiveis', [VotacaoController::class, 'disponiveis'])->name('votacoes.disponiveis');
    Route::get('/votacoes/abertas', [VotacaoController::class, 'abertas'])->name('votacoes.abertas');
    Route::put('/votacoes/abertas/{rodada}/encerramento', [VotacaoController::class, 'atualizarEncerramento'])->name('votacoes.atualizar-encerramento');
    Route::get('/votacoes/disponiveis/{rodada}/votar', [VotacaoController::class, 'votar'])->name('votacoes.votar');
    Route::post('/votacoes/disponiveis/{rodada}/votar', [VotacaoController::class, 'registrarVoto'])->name('votacoes.votar.store');

    Route::post('/documentos', [DocumentoController::class, 'store'])->name('documentos.store');
    Route::get('/documentos/{documento}/download', [DocumentoController::class, 'download'])->name('documentos.download');
    Route::delete('/documentos/{documento}', [DocumentoController::class, 'destroy'])->name('documentos.destroy');

    Route::get('/perfil', [PerfilController::class, 'edit'])->name('perfil');
    Route::put('/perfil', [PerfilController::class, 'update'])->name('perfil.update');

    Route::get('/resultados', function () {
        return view('resultados.results');
    })->name('resultados');

    Route::get('/configuracoes', function () {
        return view('configuracoes');
    })->name('configuracoes');
});