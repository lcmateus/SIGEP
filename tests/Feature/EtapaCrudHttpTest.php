<?php

use App\Enums\EtapaTipo;
use App\Enums\ProcessoStatus;
use App\Models\Etapa;
use App\Models\Processo;
use App\Models\User;

describe('EtapaController CRUD', function () {
    test('index retorna etapas do processo', function () {
        $admin = User::factory()->admin()->create();
        $processo = Processo::factory()->create(['id_administrador' => $admin->id]);
        Etapa::factory()->count(2)->create(['processo_id' => $processo->id]);

        $response = $this->actingAs($admin)
            ->get(route('etapas.index', $processo));

        $response->assertStatus(200);
        expect($response->json())->toHaveCount(2);
    });

    test('show retorna etapa com relacionamentos', function () {
        $admin = User::factory()->admin()->create();
        $etapa = Etapa::factory()->create();

        $response = $this->actingAs($admin)->get(route('etapas.show', $etapa));

        $response->assertStatus(200);
        expect($response->json('tipo'))->not->toBeNull();
    });

    test('store cria etapa vinculada ao processo', function () {
        $admin = User::factory()->admin()->create();
        $processo = Processo::factory()->create(['id_administrador' => $admin->id]);

        $response = $this->actingAs($admin)
            ->post(route('etapas.store', $processo), [
                'tipo' => EtapaTipo::JuizoAdmissibilidade->value,
            ]);

        $response->assertRedirect();
        expect($processo->etapas()->count())->toBe(1)
            ->and($processo->etapas()->first()->tipo)->toBe(EtapaTipo::JuizoAdmissibilidade);
    });

    test('store rejeita tipo invalido', function () {
        $admin = User::factory()->admin()->create();
        $processo = Processo::factory()->create(['id_administrador' => $admin->id]);

        $response = $this->actingAs($admin)
            ->post(route('etapas.store', $processo), [
                'tipo' => 'tipo_inexistente',
            ]);

        $response->assertSessionHasErrors('tipo');
    });

    test('update altera tipo da etapa', function () {
        $admin = User::factory()->admin()->create();
        $etapa = Etapa::factory()->create([
            'tipo' => EtapaTipo::JuizoAdmissibilidade,
        ]);

        $response = $this->actingAs($admin)
            ->put(route('etapas.update', $etapa), [
                'tipo' => EtapaTipo::ProcedimentoPreliminar->value,
            ]);

        $response->assertRedirect();
        expect($etapa->fresh()->tipo)->toBe(EtapaTipo::ProcedimentoPreliminar);
    });

    test('destroy remove etapa', function () {
        $admin = User::factory()->admin()->create();
        $etapa = Etapa::factory()->create();

        $response = $this->actingAs($admin)
            ->delete(route('etapas.destroy', $etapa));

        $response->assertRedirect();
        expect(Etapa::find($etapa->id))->toBeNull();
    });
});
