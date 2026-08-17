<?php

use App\Enums\ProcessoStatus;
use App\Models\Processo;
use App\Models\User;

describe('ProcessoController CRUD', function () {
    test('index retorna lista paginada de processos', function () {
        $admin = User::factory()->admin()->create();
        Processo::factory()->count(3)->create(['id_administrador' => $admin->id]);

        $response = $this->actingAs($admin)->get(route('processos.index'));

        $response->assertStatus(200);
        expect($response->json('data'))->toHaveCount(3);
    });

    test('show retorna processo com relacionamentos', function () {
        $admin = User::factory()->admin()->create();
        $processo = Processo::factory()->create(['id_administrador' => $admin->id]);

        $response = $this->actingAs($admin)->get(route('processos.show', $processo));

        $response->assertStatus(200);
        expect($response->json('numero_sei'))->toBe($processo->numero_sei)
            ->and($response->json('administrador'))->not->toBeNull();
    });

    test('update altera dados do processo', function () {
        $admin = User::factory()->admin()->create();
        $relator = User::factory()->create();
        $processo = Processo::factory()->create(['id_administrador' => $admin->id]);

        $response = $this->actingAs($admin)->put(route('processos.update', $processo), [
            'id_relator' => $relator->id,
        ]);

        $response->assertRedirect();
        expect($processo->fresh()->id_relator)->toBe($relator->id);
    });

    test('destroy remove processo', function () {
        $admin = User::factory()->admin()->create();
        $processo = Processo::factory()->create(['id_administrador' => $admin->id]);

        $response = $this->actingAs($admin)->delete(route('processos.destroy', $processo));

        $response->assertRedirect();
        expect(Processo::find($processo->id))->toBeNull();
    });

    test('abrir votacao transita para EmVotacao', function () {
        $admin = User::factory()->admin()->create();
        $processo = Processo::factory()->create([
            'id_administrador' => $admin->id,
            'status' => ProcessoStatus::EmElaboracao,
        ]);

        $response = $this->actingAs($admin)
            ->post(route('processos.abrir_votacao', $processo));

        $response->assertRedirect();
        expect($processo->fresh()->status)->toBe(ProcessoStatus::EmVotacao);
    });

    test('arquivar via rota', function () {
        $admin = User::factory()->admin()->create();
        $processo = Processo::factory()->create([
            'id_administrador' => $admin->id,
            'status' => ProcessoStatus::AguardandoDevolucaoSecretaria,
        ]);

        $response = $this->actingAs($admin)
            ->post(route('processos.arquivar', $processo));

        $response->assertRedirect();
        expect($processo->fresh()->status)->toBe(ProcessoStatus::Arquivado);
    });
});
