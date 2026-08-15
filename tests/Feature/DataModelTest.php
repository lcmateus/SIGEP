<?php

use App\Enums\DocumentoTipo;
use App\Enums\EtapaTipo;
use App\Enums\ProcessoStatus;
use App\Enums\ResultadoVotacao;
use App\Enums\UserStatus;
use App\Enums\UserTipo;
use App\Enums\VotoOpcao;
use App\Models\Documento;
use App\Models\Etapa;
use App\Models\Processo;
use App\Models\RodadaVotacao;
use App\Models\User;
use App\Models\Voto;

test('modelo representa relacionamentos principais do SIGEP', function () {
    $admin = User::factory()->admin()->create();
    $relator = User::factory()->create();

    $processo = Processo::query()->create([
        'numero_sei' => '23172.000001/2026-10',
        'data_admissao' => '2026-08-15',
        'status' => ProcessoStatus::EmVotacao,
        'id_administrador' => $admin->id,
        'id_relator' => $relator->id,
    ]);

    $etapa = Etapa::query()->create([
        'tipo' => EtapaTipo::JuizoAdmissibilidade,
        'status' => ProcessoStatus::EmVotacao,
        'processo_id' => $processo->id,
    ]);

    $rodada = RodadaVotacao::query()->create([
        'data_abertura' => '2026-08-15 09:00:00',
        'data_encerramento' => '2026-08-20 18:00:00',
        'resultado' => ResultadoVotacao::Pendente,
        'etapa_id' => $etapa->id,
        'presidente_id' => $relator->id,
    ]);

    $documento = Documento::query()->create([
        'tipo' => DocumentoTipo::Relatorio,
        'data_insercao' => '2026-08-15 10:00:00',
        'anexo' => 'documentos/relatorio.pdf',
        'etapa_id' => $etapa->id,
    ]);

    $voto = Voto::query()->create([
        'opcao' => VotoOpcao::FavorComRessalvas,
        'justificativa' => 'Aprovado com ajuste de redacao.',
        'is_minerva' => true,
        'membro_id' => $relator->id,
        'rodada_id' => $rodada->id,
    ]);

    expect($admin->processosAdministrados()->first()->is($processo))->toBeTrue()
        ->and($relator->processosRelatados()->first()->is($processo))->toBeTrue()
        ->and($processo->administrador->is($admin))->toBeTrue()
        ->and($processo->relator->is($relator))->toBeTrue()
        ->and($processo->etapas()->first()->is($etapa))->toBeTrue()
        ->and($etapa->processo->is($processo))->toBeTrue()
        ->and($etapa->rodadasVotacao()->first()->is($rodada))->toBeTrue()
        ->and($etapa->documentos()->first()->is($documento))->toBeTrue()
        ->and($rodada->etapa->is($etapa))->toBeTrue()
        ->and($rodada->presidente->is($relator))->toBeTrue()
        ->and($rodada->votos()->first()->is($voto))->toBeTrue()
        ->and($voto->membro->is($relator))->toBeTrue()
        ->and($voto->rodada->is($rodada))->toBeTrue()
        ->and($documento->etapa->is($etapa))->toBeTrue();
});

test('models fazem cast dos enums e campos booleanos', function () {
    $admin = User::factory()->admin()->create();
    $membro = User::factory()->create();
    $processo = Processo::query()->create([
        'numero_sei' => '23172.000002/2026-10',
        'id_administrador' => $admin->id,
        'id_relator' => $membro->id,
    ]);
    $etapa = Etapa::query()->create([
        'tipo' => EtapaTipo::Relatoria,
        'processo_id' => $processo->id,
    ]);
    $rodada = RodadaVotacao::query()->create([
        'etapa_id' => $etapa->id,
        'presidente_id' => $membro->id,
    ]);
    $voto = Voto::query()->create([
        'opcao' => VotoOpcao::Abstencao,
        'is_minerva' => false,
        'membro_id' => $membro->id,
        'rodada_id' => $rodada->id,
    ]);

    expect($admin->tipo)->toBe(UserTipo::Admin)
        ->and($processo->status)->toBe(ProcessoStatus::EmElaboracao)
        ->and($etapa->tipo)->toBe(EtapaTipo::Relatoria)
        ->and($rodada->resultado)->toBe(ResultadoVotacao::Pendente)
        ->and($voto->opcao)->toBe(VotoOpcao::Abstencao)
        ->and($voto->is_minerva)->toBeFalse();
});

test('rodada de votacao escolhe presidente aleatoriamente entre membros ativos', function () {
    $admin = User::factory()->admin()->create();
    $membroAtivo = User::factory()->create();
    $membroPendente = User::factory()->pendente()->create();

    $processo = Processo::query()->create([
        'numero_sei' => '23172.000003/2026-10',
        'id_administrador' => $admin->id,
        'id_relator' => $membroAtivo->id,
    ]);

    $etapa = Etapa::query()->create([
        'tipo' => EtapaTipo::ProcedimentoPreliminar,
        'processo_id' => $processo->id,
    ]);

    $rodada = $etapa->rodadasVotacao()->create([
        'data_abertura' => now(),
    ]);

    expect($rodada->presidente_id)->toBe($membroAtivo->id)
        ->and($rodada->presidente->tipo)->toBe(UserTipo::Membro)
        ->and($rodada->presidente->status)->toBe(UserStatus::Ativo)
        ->and($rodada->presidente_id)->not->toBe($admin->id)
        ->and($rodada->presidente_id)->not->toBe($membroPendente->id);
});
