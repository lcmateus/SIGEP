<?php

use App\Enums\UserStatus;
use App\Enums\UserTipo;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

describe('login', function () {
    test('usuario ativo faz login com credenciais validas', function () {
        $user = User::factory()->create([
            'siape' => '1234567',
            'password' => Hash::make('password123'),
            'status' => UserStatus::Ativo,
        ]);

        $response = $this->post(route('login'), [
            'siape' => '1234567',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    });

    test('senha incorreta nao autentica e volta com erro', function () {
        User::factory()->create([
            'siape' => '1234567',
            'password' => Hash::make('password123'),
            'status' => UserStatus::Ativo,
        ]);

        $response = $this->post(route('login'), [
            'siape' => '1234567',
            'password' => 'senha-errada',
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHasErrors('siape');
        $this->assertGuest();
    });

    test('usuario pendente e deslogado apos tentativa de login', function () {
        User::factory()->create([
            'siape' => '1234567',
            'password' => Hash::make('password123'),
            'status' => UserStatus::Pendente,
        ]);

        $response = $this->post(route('login'), [
            'siape' => '1234567',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHasErrors('siape');
        $this->assertGuest();
    });

    test('siape inexistente nao autentica', function () {
        $response = $this->post(route('login'), [
            'siape' => '9999999',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('siape');
        $this->assertGuest();
    });

    test('siape e obrigatorias', function () {
        $response = $this->post(route('login'), [
            'siape' => '',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['siape', 'password']);
        $this->assertGuest();
    });
});
