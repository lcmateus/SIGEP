<?php

use App\Models\Processo;
use App\Models\UsuarioAdministrador;
use App\Support\EncryptedId;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('descriptografa o id criptografado na rota e encontra o registro', function () {
    $admin = UsuarioAdministrador::query()->create([
        'siape' => '1234567',
        'nome' => 'Admin Teste',
        'email' => 'admin@teste.com',
        'password' => 'password',
    ]);

    $processo = Processo::query()->create([
        'numero_sei' => '00010.123456/2026-01',
        'data_admissao' => now()->toDateString(),
        'id_administrador' => $admin->siape,
        'id_relator' => null,
    ]);

    $this->actingAs($admin)
        ->get('/processos/'.EncryptedId::encrypt($processo->numero_sei))
        ->assertOk();
});

it ('retorna 404 para id criptografado de registro inexistente', function () {
    $admin = UsuarioAdministrador::query()->create([
        'siape' => '7654321',
        'nome' => 'Admin Teste 2',
        'email' => 'admin2@teste.com',
        'password' => 'password',
    ]);

    $this->actingAs($admin)
        ->get('/processos/'.EncryptedId::encrypt('zzz.nao-existe'))
        ->assertNotFound();
});

it('retorna 404 para valor invalido na url', function () {
    $admin = UsuarioAdministrador::query()->create([
        'siape' => '1111111',
        'nome' => 'Admin Teste 3',
        'email' => 'admin3@teste.com',
        'password' => 'password',
    ]);

    $this->actingAs($admin)
        ->get('/processos/valor-invalido')
        ->assertNotFound();
});

it('gera urls com id criptografado', function () {
    $processo = new Processo(['numero_sei' => '00099.999999/2026-99']);

    expect(route('processos.show', $processo))
        ->not->toContain('00099.999999')
        ->and(str_contains(route('processos.show', $processo), '/'))
        ->toBeTrue();
});
