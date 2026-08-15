<?php

use App\Enums\UserStatus;
use App\Enums\UserTipo;
use App\Models\User;

describe('UsuarioController CRUD completo', function () {
    test('index retorna lista paginada de usuarios', function () {
        $admin = User::factory()->admin()->create();
        User::factory()->count(3)->create();

        $response = $this->actingAs($admin)->get(route('usuarios.api.index'));

        $response->assertStatus(200);
        expect($response->json('data'))->toHaveCount(4);
    });

    test('index filtra por tipo', function () {
        $admin = User::factory()->admin()->create();
        User::factory()->count(2)->create();
        User::factory()->admin()->create();

        $response = $this->actingAs($admin)
            ->get(route('usuarios.api.index', ['tipo' => UserTipo::Membro->value]));

        $response->assertStatus(200);
        foreach ($response->json('data') as $user) {
            expect($user['tipo'])->toBe(UserTipo::Membro->value);
        }
    });

    test('index busca por nome', function () {
        $admin = User::factory()->admin()->create();
        User::factory()->create(['nome' => 'Joao Especial']);
        User::factory()->create(['nome' => 'Maria Comum']);

        $response = $this->actingAs($admin)
            ->get(route('usuarios.api.index', ['busca' => 'Especial']));

        $response->assertStatus(200);
        expect($response->json('data'))->toHaveCount(1)
            ->and($response->json('data.0.nome'))->toBe('Joao Especial');
    });

    test('show retorna usuario com relacionamentos', function () {
        $admin = User::factory()->admin()->create();
        $usuario = User::factory()->create();

        $response = $this->actingAs($admin)
            ->get(route('usuarios.api.show', $usuario));

        $response->assertStatus(200);
        expect($response->json('id'))->toBe($usuario->id);
    });

    test('update altera nome e email', function () {
        $admin = User::factory()->admin()->create();
        $usuario = User::factory()->create([
            'nome' => 'Nome Antigo',
            'email' => 'antigo@ifpi.edu.br',
        ]);

        $response = $this->actingAs($admin)
            ->put(route('usuarios.update', $usuario), [
                'nome' => 'Nome Novo',
                'email' => 'novo@ifpi.edu.br',
            ]);

        $response->assertRedirect();
        expect($usuario->fresh()->nome)->toBe('Nome Novo')
            ->and($usuario->fresh()->email)->toBe('novo@ifpi.edu.br');
    });

    test('update altera senha', function () {
        $admin = User::factory()->admin()->create();
        $usuario = User::factory()->create();

        $response = $this->actingAs($admin)
            ->put(route('usuarios.update', $usuario), [
                'password' => 'novasenha123',
                'password_confirmation' => 'novasenha123',
            ]);

        $response->assertRedirect();
        expect(\Illuminate\Support\Facades\Hash::check('novasenha123', $usuario->fresh()->password))->toBeTrue();
    });

    test('update rejeita email duplicado', function () {
        $admin = User::factory()->admin()->create();
        User::factory()->create(['email' => 'ocupado@ifpi.edu.br']);
        $usuario = User::factory()->create();

        $response = $this->actingAs($admin)
            ->put(route('usuarios.update', $usuario), [
                'email' => 'ocupado@ifpi.edu.br',
            ]);

        $response->assertSessionHasErrors('email');
    });

    test('deactivate torna usuario pendente', function () {
        $admin = User::factory()->admin()->create();
        $usuario = User::factory()->create(['status' => UserStatus::Ativo]);

        $response = $this->actingAs($admin)
            ->post(route('usuarios.deactivate', $usuario));

        $response->assertRedirect();
        expect($usuario->fresh()->status)->toBe(UserStatus::Pendente)
            ->and($usuario->fresh()->isAtivo())->toBeFalse();
    });

    test('destroy remove usuario', function () {
        $admin = User::factory()->admin()->create();
        $usuario = User::factory()->create();

        $response = $this->actingAs($admin)
            ->delete(route('usuarios.destroy', $usuario));

        $response->assertRedirect();
        expect(User::find($usuario->id))->toBeNull();
    });
});
