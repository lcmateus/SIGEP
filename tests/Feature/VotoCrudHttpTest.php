<?php

use App\Enums\VotoOpcao;
use App\Models\Etapa;
use App\Models\User;
use App\Models\Voto;

describe('VotoController', function () {
    test('index retorna votos da rodada', function () {
        $admin = User::factory()->admin()->create();
        $membro = User::factory()->create();
        $etapa = Etapa::factory()->create();
        $rodada = $etapa->rodadasVotacao()->create([
            'data_abertura' => now(),
            'presidente_id' => $membro->id,
        ]);
        for ($i = 0; $i < 3; $i++) {
            Voto::factory()->create([
                'rodada_id' => $rodada->id,
                'membro_id' => User::factory()->create()->id,
            ]);
        }

        $response = $this->actingAs($admin)
            ->get(route('votos.index', $rodada));

        $response->assertStatus(200);
        expect($response->json())->toHaveCount(3);
    });

    test('show retorna um voto', function () {
        $admin = User::factory()->admin()->create();
        $voto = Voto::factory()->create();

        $response = $this->actingAs($admin)->get(route('votos.show', $voto));

        $response->assertStatus(200);
        expect($response->json('id'))->toBe($voto->id);
    });

    test('store registra voto comum', function () {
        $membro = User::factory()->create();
        $etapa = Etapa::factory()->create();
        $rodada = $etapa->rodadasVotacao()->create([
            'data_abertura' => now(),
            'presidente_id' => $membro->id,
        ]);

        $response = $this->actingAs($membro)
            ->post(route('votos.store', $rodada), [
                'opcao' => VotoOpcao::Favor->value,
                'justificativa' => 'Concordo plenamente.',
            ]);

        $response->assertRedirect();
        expect(Voto::query()->where('rodada_id', $rodada->id)->count())->toBe(1)
            ->and(Voto::first()->is_minerva)->toBeFalse();
    });

    test('store rejeita opcao invalida', function () {
        $membro = User::factory()->create();
        $etapa = Etapa::factory()->create();
        $rodada = $etapa->rodadasVotacao()->create([
            'data_abertura' => now(),
            'presidente_id' => $membro->id,
        ]);

        $response = $this->actingAs($membro)
            ->post(route('votos.store', $rodada), [
                'opcao' => 'invalido',
            ]);

        $response->assertSessionHasErrors('opcao');
    });

    test(' voto minerva rejeitado para nao presidente', function () {
        $presidente = User::factory()->create();
        $outro = User::factory()->create();
        $etapa = Etapa::factory()->create();
        $rodada = $etapa->rodadasVotacao()->create([
            'data_abertura' => now(),
            'presidente_id' => $presidente->id,
        ]);

        $response = $this->actingAs($outro)
            ->post(route('votos.store', $rodada), [
                'opcao' => VotoOpcao::Favor->value,
                'is_minerva' => true,
            ]);

        $response->assertStatus(403);
    });

    test(' voto minerva aceito para presidente da rodada', function () {
        $presidente = User::factory()->create();
        $etapa = Etapa::factory()->create();
        $rodada = $etapa->rodadasVotacao()->create([
            'data_abertura' => now(),
            'presidente_id' => $presidente->id,
        ]);

        $response = $this->actingAs($presidente)
            ->post(route('votos.store', $rodada), [
                'opcao' => VotoOpcao::Favor->value,
                'is_minerva' => true,
            ]);

        $response->assertRedirect();
        expect(Voto::first()->is_minerva)->toBeTrue();
    });
});
