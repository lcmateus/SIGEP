<?php

use App\Enums\ProcessoStatus;
use App\Models\Processo;
use App\Models\User;

describe('ProcessoController store', function () {
    test('cria processo com usuario autenticado', function () {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('processos.store'), [
            'numero_sei' => '23172.000001/2026-10',
            'data_admissao' => '2026-08-15',
            'data_devolucao' => '2026-09-15',
        ]);

        $response->assertRedirect(route('processos.create'));

        $processo = Processo::query()->where('numero_sei', '23172.000001/2026-10')->firstOrFail();
        expect($processo->status)->toBe(ProcessoStatus::EmElaboracao)
            ->and($processo->id_administrador)->toBe($admin->id)
            ->and($processo->id_relator)->toBeNull();
    });

    test('numero_sei duplicado rejeitado', function () {
        $admin = User::factory()->admin()->create();
        Processo::factory()->create([
            'numero_sei' => '23172.000001/2026-10',
            'id_administrador' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->post(route('processos.store'), [
            'numero_sei' => '23172.000001/2026-10',
        ]);

        $response->assertSessionHasErrors('numero_sei');
        expect(Processo::query()->where('numero_sei', '23172.000001/2026-10')->count())->toBe(1);
    });

    test('numero_sei obrigatorio', function () {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('processos.store'), []);

        $response->assertSessionHasErrors('numero_sei');
    });

    test('data_devolucao nao pode ser anterior a data_admissao', function () {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('processos.store'), [
            'numero_sei' => '23172.000001/2026-10',
            'data_admissao' => '2026-09-15',
            'data_devolucao' => '2026-08-15',
        ]);

        $response->assertSessionHasErrors('data_devolucao');
    });

    test('id_relator deve existir na tabela users', function () {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('processos.store'), [
            'numero_sei' => '23172.000001/2026-10',
            'id_relator' => 99999,
        ]);

        $response->assertSessionHasErrors('id_relator');
    });

    test('id_relator valido aceito', function () {
        $admin = User::factory()->admin()->create();
        $relator = User::factory()->create();

        $response = $this->actingAs($admin)->post(route('processos.store'), [
            'numero_sei' => '23172.000001/2026-10',
            'id_relator' => $relator->id,
        ]);

        $response->assertRedirect(route('processos.create'));

        $processo = Processo::query()->firstOrFail();
        expect($processo->id_relator)->toBe($relator->id);
    });

    test('sem usuario autenticado falha (id_administrador not null)', function () {
        $response = $this->post(route('processos.store'), [
            'numero_sei' => '23172.000001/2026-10',
        ]);

        expect(Processo::query()->count())->toBe(0);
    });
});
