<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home.home');
})->name('home');

Route::get('/cadastro', function () {
    return view('home.cadastro');
})->name('cadastro');

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/processos', function () {
    return view('processos');
})->name('processos.index');

Route::get('/processos/criar', function () {
    return view('processos.create');
})->name('processos.create');

Route::get('/processos/visualizar', function () {
    return view('processos.show');
})->name('processos.show');

Route::get('/usuarios', function () {
    return view('usuarios');
})->name('usuarios.index');

Route::get('/usuarios/cadastro', function () {
    return view('usuarios.create');
})->name('usuarios.create');

Route::get('/votacao', function () {
    return view('votacao');
})->name('votacao');

Route::get('/perfil', function () {
    return view('perfil');
})->name('perfil');

Route::get('/relatorios', function () {
    return view('relatorios');
})->name('relatorios');

Route::get('/configuracoes', function () {
    return view('configuracoes');
})->name('configuracoes');