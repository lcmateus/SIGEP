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

Route::get('/processos/criar', function () {
    return view('processos.create');
})->name('processos.create');

Route::get('/usuarios', function () {
    return view('usuarios.list');
})->name('usuarios.list');

Route::get('/usuarios/cadastro', function () {
    return view('usuarios.create');
})->name('usuarios.create');

Route::get('/votacao', function () {
    return view('votacao');
})->name('votacao');

Route::get('/perfil', function () {
    return view('perfil.edit');
})->name('perfil');

Route::get('/resultados', function () {
    return view('resultados.results');
})->name('resultados');

Route::get('/configuracoes', function () {
    return view('configuracoes');
})->name('configuracoes');