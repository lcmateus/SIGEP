<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\EtapaController;
use App\Http\Controllers\ProcessoController;
use App\Http\Controllers\RodadaVotacaoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\VotoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home.home');
})->name('home');

Route::get('/cadastro', function () {
    return view('home.cadastro');
})->name('cadastro');

Route::post('/cadastro', [AuthController::class, 'register'])->name('register');
Route::post('/', [AuthController::class, 'login'])->name('login');

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/processos/criar', function () {
    return view('processos.create');
})->name('processos.create');

Route::get('/processos', [ProcessoController::class, 'index'])->name('processos.index');
Route::post('/processos/criar', [ProcessoController::class, 'store'])->name('processos.store');
Route::get('/processos/{processo}', [ProcessoController::class, 'show'])->name('processos.show');
Route::put('/processos/{processo}', [ProcessoController::class, 'update'])->name('processos.update');
Route::delete('/processos/{processo}', [ProcessoController::class, 'destroy'])->name('processos.destroy');
Route::post('/processos/{processo}/abrir-votacao', [ProcessoController::class, 'abrirVotacao'])->name('processos.abrir_votacao');
Route::post('/processos/{processo}/arquivar', [ProcessoController::class, 'arquivar'])->name('processos.arquivar');

Route::get('/processos/{processo}/etapas', [EtapaController::class, 'index'])->name('etapas.index');
Route::post('/processos/{processo}/etapas', [EtapaController::class, 'store'])->name('etapas.store');
Route::get('/etapas/{etapa}', [EtapaController::class, 'show'])->name('etapas.show');
Route::put('/etapas/{etapa}', [EtapaController::class, 'update'])->name('etapas.update');
Route::delete('/etapas/{etapa}', [EtapaController::class, 'destroy'])->name('etapas.destroy');

Route::get('/etapas/{etapa}/rodadas', [RodadaVotacaoController::class, 'index'])->name('rodadas.index');
Route::post('/etapas/{etapa}/rodadas', [RodadaVotacaoController::class, 'store'])->name('rodadas.store');
Route::get('/rodadas/{rodadaVotacao}', [RodadaVotacaoController::class, 'show'])->name('rodadas.show');
Route::post('/rodadas/{rodadaVotacao}/encerrar', [RodadaVotacaoController::class, 'encerrar'])->name('rodadas.encerrar');
Route::post('/rodadas/{rodadaVotacao}/resultado', [RodadaVotacaoController::class, 'registrarResultado'])->name('rodadas.resultado');
Route::delete('/rodadas/{rodadaVotacao}', [RodadaVotacaoController::class, 'destroy'])->name('rodadas.destroy');

Route::get('/rodadas/{rodadaVotacao}/votos', [VotoController::class, 'index'])->name('votos.index');
Route::post('/rodadas/{rodadaVotacao}/votos', [VotoController::class, 'store'])->name('votos.store');
Route::get('/votos/{voto}', [VotoController::class, 'show'])->name('votos.show');

Route::get('/etapas/{etapa}/documentos', [DocumentoController::class, 'index'])->name('documentos.index');
Route::post('/etapas/{etapa}/documentos', [DocumentoController::class, 'store'])->name('documentos.store');
Route::get('/documentos/{documento}', [DocumentoController::class, 'show'])->name('documentos.show');
Route::get('/documentos/{documento}/download', [DocumentoController::class, 'download'])->name('documentos.download');
Route::delete('/documentos/{documento}', [DocumentoController::class, 'destroy'])->name('documentos.destroy');

Route::get('/usuarios', function () {
    return view('usuarios.list');
})->name('usuarios.list');

Route::get('/api/usuarios', [UsuarioController::class, 'index'])->name('usuarios.api.index');
Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
Route::get('/api/usuarios/{usuario}', [UsuarioController::class, 'show'])->name('usuarios.api.show');
Route::put('/usuarios/{usuario}', [UsuarioController::class, 'update'])->name('usuarios.update');
Route::delete('/usuarios/{usuario}', [UsuarioController::class, 'destroy'])->name('usuarios.destroy');
Route::post('/usuarios/{usuario}/approve', [UsuarioController::class, 'approve'])->name('usuarios.approve');
Route::post('/usuarios/{usuario}/deactivate', [UsuarioController::class, 'deactivate'])->name('usuarios.deactivate');
Route::post('/usuarios/{usuario}/transferir-secretaria', [UsuarioController::class, 'transferirSecretaria'])->name('usuarios.transferir_secretaria');

Route::get('/usuarios/cadastro', function () {
    return view('usuarios.create');
})->name('usuarios.create');

Route::get('/votar', function () {
    return view('processos.votar');
})->name('votar');

Route::get('/perfil', function () {
    return view('perfil.edit');
})->name('perfil');

Route::get('/resultados', function () {
    return view('resultados.results');
})->name('resultados');

Route::get('/configuracoes', function () {
    return view('configuracoes');
})->name('configuracoes');
