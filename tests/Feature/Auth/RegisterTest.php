<?php

use App\Enums\UserStatus;
use App\Enums\UserTipo;
use App\Models\User;

describe('registro', function () {
    test('primeiro cadastro cria admin ativo', function () {
        $response = $this->post(route('register'), [
            'siape' => '0000001',
            'name' => 'Secretario Geral',
            'email' => 'secretario@ifpi.edu.br',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('home'));

        $user = User::query()->firstOrFail();
        expect($user->tipo)->toBe(UserTipo::Admin)
            ->and($user->status)->toBe(UserStatus::Ativo)
            ->and($user->isAdmin())->toBeTrue()
            ->and($user->isAtivo())->toBeTrue();
    });

    test('segundo cadastro cria membro pendente', function () {
        User::factory()->admin()->create();

        $response = $this->post(route('register'), [
            'siape' => '0000002',
            'name' => 'Membro da Comissao',
            'email' => 'membro@ifpi.edu.br',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('home'));

        $user = User::query()->where('siape', '0000002')->firstOrFail();
        expect($user->tipo)->toBe(UserTipo::Membro)
            ->and($user->status)->toBe(UserStatus::Pendente)
            ->and($user->isMembro())->toBeTrue()
            ->and($user->isAtivo())->toBeFalse();
    });

    test('siape duplicado rejeitado', function () {
        User::factory()->create(['siape' => '1234567']);

        $response = $this->post(route('register'), [
            'siape' => '1234567',
            'name' => 'Outro Usuario',
            'email' => 'outro@ifpi.edu.br',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('siape');
    });

    test('email duplicado rejeitado', function () {
        User::factory()->create(['email' => 'existente@ifpi.edu.br']);

        $response = $this->post(route('register'), [
            'siape' => '9999999',
            'name' => 'Outro Usuario',
            'email' => 'existente@ifpi.edu.br',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    });

    test('senha sem confirmacao rejeitada', function () {
        $response = $this->post(route('register'), [
            'siape' => '0000001',
            'name' => 'Teste',
            'email' => 'teste@ifpi.edu.br',
            'password' => 'password123',
            'password_confirmation' => 'diferente',
        ]);

        $response->assertSessionHasErrors('password');
    });

    test('senha menor que 8 caracteres rejeitada', function () {
        $response = $this->post(route('register'), [
            'siape' => '0000001',
            'name' => 'Teste',
            'email' => 'teste@ifpi.edu.br',
            'password' => '1234567',
            'password_confirmation' => '1234567',
        ]);

        $response->assertSessionHasErrors('password');
    });

    test('campos obrigatorios validados', function () {
        $response = $this->post(route('register'), []);

        $response->assertSessionHasErrors(['siape', 'name', 'email', 'password']);
    });

    test('tipo_membro e opcional (nullable)', function () {
        $response = $this->post(route('register'), [
            'siape' => '0000001',
            'name' => 'Teste',
            'email' => 'teste@ifpi.edu.br',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('home'));
        expect(User::query()->count())->toBe(1);
    });
});
