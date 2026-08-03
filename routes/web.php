<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'auth.login')->name('login');

Route::view('/cadastro', 'auth.register')->name('register');

Route::view('/recuperar-senha', 'auth.forgot-password')->name('forgot-password');