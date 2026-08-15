<?php

use App\Models\User;

describe('middleware de autenticacao', function () {
    test('rota dashboard acessivel sem login (sem protecao)', function () {
        $response = $this->get(route('dashboard'));
        $response->assertStatus(200);
        $this->assertGuest();
    });

    test('rota processos.create acessivel sem login (sem protecao)', function () {
        $response = $this->get(route('processos.create'));
        $response->assertStatus(200);
        $this->assertGuest();
    });

    test('rota usuarios.list acessivel sem login (sem protecao)', function () {
        $response = $this->get(route('usuarios.list'));
        $response->assertStatus(200);
        $this->assertGuest();
    });

    test('rota votar acessivel sem login (sem protecao)', function () {
        $response = $this->get(route('votar'));
        $response->assertStatus(200);
        $this->assertGuest();
    });

    test('rota perfil acessivel sem login (sem protecao)', function () {
        $response = $this->get(route('perfil'));
        $response->assertStatus(200);
        $this->assertGuest();
    });

    test('rota resultados acessivel sem login (sem protecao)', function () {
        $response = $this->get(route('resultados'));
        $response->assertStatus(200);
        $this->assertGuest();
    });

    test('rota home (login) acessivel sem login', function () {
        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $this->assertGuest();
    });

    test('rota cadastro acessivel sem login', function () {
        $response = $this->get(route('cadastro'));
        $response->assertStatus(200);
        $this->assertGuest();
    });

    test('POST login com usuario autenticado redireciona para dashboard', function () {
        $user = User::factory()->admin()->create();

        $response = $this->actingAs($user)->post(route('login'), [
            'siape' => $user->siape,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    });
});
