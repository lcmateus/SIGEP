<?php

use App\Enums\UserStatus;
use App\Enums\UserTipo;
use App\Models\User;

test('primeiro cadastro cria administrador ativo sem seed hardcoded', function () {
    $this->post(route('register'), [
        'siape' => '0000001',
        'name' => 'Secretario Geral',
        'email' => 'secretario@ifpi.edu.br',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertRedirect(route('home'));

    $user = User::query()->firstOrFail();

    expect($user->tipo)->toBe(UserTipo::Admin)
        ->and($user->status)->toBe(UserStatus::Ativo)
        ->and($user->isAdmin())->toBeTrue()
        ->and($user->isAtivo())->toBeTrue();
});

test('cadastros seguintes criam membros pendentes', function () {
    User::factory()->admin()->create();

    $this->post(route('register'), [
        'siape' => '0000002',
        'name' => 'Membro da Comissao',
        'email' => 'membro@ifpi.edu.br',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertRedirect(route('home'));

    $user = User::query()->where('siape', '0000002')->firstOrFail();

    expect($user->tipo)->toBe(UserTipo::Membro)
        ->and($user->status)->toBe(UserStatus::Pendente)
        ->and($user->isMembro())->toBeTrue()
        ->and($user->isAtivo())->toBeFalse();
});
