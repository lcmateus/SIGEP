<?php

use App\Enums\UserStatus;
use App\Enums\UserTipo;
use App\Models\User;

describe('UsuarioController store', function () {
    test('cria membro pendente quando ja existe admin', function () {
        User::factory()->admin()->create();

        $response = $this->post(route('usuarios.store'), [
            'siape' => '1234567',
            'nome' => 'Joao da Silva',
            'email' => 'joao@ifpi.edu.br',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('usuarios.store'));

        $user = User::query()->where('siape', '1234567')->firstOrFail();
        expect($user->tipo)->toBe(UserTipo::Membro)
            ->and($user->status)->toBe(UserStatus::Pendente);
    });

    test('primeiro usuario criado via store vira admin ativo', function () {
        $response = $this->post(route('usuarios.store'), [
            'siape' => '0000001',
            'nome' => 'Administrador Inicial',
            'email' => 'admin@ifpi.edu.br',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('usuarios.store'));

        $user = User::query()->firstOrFail();
        expect($user->tipo)->toBe(UserTipo::Admin)
            ->and($user->status)->toBe(UserStatus::Ativo);
    });

    test('aceita campo name como alternativa a nome', function () {
        User::factory()->admin()->create();

        $response = $this->post(route('usuarios.store'), [
            'siape' => '1234567',
            'name' => 'Maria Oliveira',
            'email' => 'maria@ifpi.edu.br',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('usuarios.store'));

        $user = User::query()->where('siape', '1234567')->firstOrFail();
        expect($user->nome)->toBe('Maria Oliveira');
    });

    test('siape duplicado rejeitado', function () {
        User::factory()->create(['siape' => '1234567']);

        $response = $this->post(route('usuarios.store'), [
            'siape' => '1234567',
            'nome' => 'Outro',
            'email' => 'outro@ifpi.edu.br',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('siape');
    });

    test('email duplicado rejeitado', function () {
        User::factory()->create(['email' => 'existente@ifpi.edu.br']);

        $response = $this->post(route('usuarios.store'), [
            'siape' => '9999999',
            'nome' => 'Outro',
            'email' => 'existente@ifpi.edu.br',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    });

    test('senha sem confirmacao rejeitada', function () {
        $response = $this->post(route('usuarios.store'), [
            'siape' => '1234567',
            'nome' => 'Teste',
            'email' => 'teste@ifpi.edu.br',
            'password' => 'password123',
            'password_confirmation' => 'diferente',
        ]);

        $response->assertSessionHasErrors('password');
    });

    test('nome e name ambos ausentes rejeitado', function () {
        $response = $this->post(route('usuarios.store'), [
            'siape' => '1234567',
            'email' => 'teste@ifpi.edu.br',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['nome', 'name']);
    });
});

describe('UsuarioController approve', function () {
    test('approve altera status de pendente para ativo', function () {
        $admin = User::factory()->admin()->create();
        $pendente = User::factory()->pendente()->create();

        $response = $this->actingAs($admin)->post(
            route('usuarios.approve', ['usuario' => $pendente->id])
        );

        $pendente->refresh();
        expect($pendente->status)->toBe(UserStatus::Ativo)
            ->and($pendente->isAtivo())->toBeTrue();
    });
});
