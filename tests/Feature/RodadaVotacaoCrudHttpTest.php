<?php

use App\Enums\ResultadoVotacao;
use App\Models\Etapa;
use App\Models\RodadaVotacao;
use App\Models\User;

describe('RodadaVotacaoController CRUD', function () {
    test('index retorna rodadas da etapa', function () {
        $admin = User::factory()->admin()->create();
        $etapa = Etapa::factory()->create();
        RodadaVotacao::factory()->count(2)->create(['etapa_id' => $etapa->id]);

        $response = $this->actingAs($admin)
            ->get(route('rodadas.index', $etapa));

        $response->assertStatus(200);
        expect($response->json())->toHaveCount(2);
    });

    test('show retorna rodada com votos e presidente', function () {
        $admin = User::factory()->admin()->create();
        $etapa = Etapa::factory()->create();
        $rodada = RodadaVotacao::factory()->create(['etapa_id' => $etapa->id]);

        $response = $this->actingAs($admin)
            ->get(route('rodadas.show', $rodada));

        $response->assertStatus(200);
        expect($response->json('id'))->toBe($rodada->id);
    });

    test('store cria rodada na etapa', function () {
        $admin = User::factory()->admin()->create();
        $membro = User::factory()->create();
        $etapa = Etapa::factory()->create();

        $response = $this->actingAs($admin)
            ->post(route('rodadas.store', $etapa), [
                'data_abertura' => '2026-08-15 10:00:00',
            ]);

        $response->assertRedirect();
        expect($etapa->rodadasVotacao()->count())->toBe(1);
    });

    test('encerrar rodada define data_encerramento e resultado', function () {
        $admin = User::factory()->admin()->create();
        $membro = User::factory()->create();
        $etapa = Etapa::factory()->create();
        $rodada = $etapa->rodadasVotacao()->create([
            'data_abertura' => now(),
            'presidente_id' => $membro->id,
        ]);

        $response = $this->actingAs($admin)
            ->post(route('rodadas.encerrar', $rodada));

        $response->assertRedirect();
        $rodada->refresh();
        expect($rodada->data_encerramento)->not->toBeNull();
    });

    test('registrar resultado define resultado da rodada', function () {
        $admin = User::factory()->admin()->create();
        $etapa = Etapa::factory()->create();
        $rodada = RodadaVotacao::factory()->create(['etapa_id' => $etapa->id]);

        $response = $this->actingAs($admin)
            ->post(route('rodadas.resultado', $rodada), [
                'resultado' => ResultadoVotacao::Aprovado->value,
            ]);

        $response->assertRedirect();
        expect($rodada->fresh()->resultado)->toBe(ResultadoVotacao::Aprovado);
    });

    test('destroy remove rodada', function () {
        $admin = User::factory()->admin()->create();
        $etapa = Etapa::factory()->create();
        $rodada = RodadaVotacao::factory()->create(['etapa_id' => $etapa->id]);

        $response = $this->actingAs($admin)
            ->delete(route('rodadas.destroy', $rodada));

        $response->assertRedirect();
        expect(RodadaVotacao::find($rodada->id))->toBeNull();
    });
});
