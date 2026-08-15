<?php

use App\Enums\DocumentoCondicao;
use App\Enums\DocumentoTipo;
use App\Enums\EtapaTipo;
use App\Enums\ProcessoStatus;
use App\Enums\ResultadoVotacao;
use App\Enums\VotoOpcao;
use App\Models\Documento;
use App\Models\Etapa;
use App\Models\Notificacao;
use App\Models\Processo;
use App\Models\RodadaVotacao;
use App\Models\User;
use App\Services\FluxoProcessual;

describe('maquina de estados', function () {
    test('transicao valida permitida', function () {
        $admin = User::factory()->admin()->create();
        $processo = Processo::factory()->create([
            'id_administrador' => $admin->id,
            'status' => ProcessoStatus::EmElaboracao,
        ]);
        $fluxo = app(FluxoProcessual::class);

        expect($fluxo->podeTransitar($processo, ProcessoStatus::EmVotacao))->toBeTrue();
    });

    test('transicao invalida rejeitada', function () {
        $admin = User::factory()->admin()->create();
        $processo = Processo::factory()->create([
            'id_administrador' => $admin->id,
            'status' => ProcessoStatus::EmElaboracao,
        ]);
        $fluxo = app(FluxoProcessual::class);

        expect($fluxo->podeTransitar($processo, ProcessoStatus::Arquivado))->toBeFalse();
    });

    test('transitar altera status do processo', function () {
        $admin = User::factory()->admin()->create();
        $processo = Processo::factory()->create([
            'id_administrador' => $admin->id,
            'status' => ProcessoStatus::EmElaboracao,
        ]);
        $fluxo = app(FluxoProcessual::class);

        $fluxo->transitar($processo, ProcessoStatus::EmVotacao);

        expect($processo->fresh()->status)->toBe(ProcessoStatus::EmVotacao);
    });

    test('transitar para status invalido lanca DomainException', function () {
        $admin = User::factory()->admin()->create();
        $processo = Processo::factory()->create([
            'id_administrador' => $admin->id,
            'status' => ProcessoStatus::EmElaboracao,
        ]);
        $fluxo = app(FluxoProcessual::class);

        $fluxo->transitar($processo, ProcessoStatus::Arquivado);
    })->throws(\DomainException::class);
});

describe('fluxo de votacao', function () {
    test('abrir votacao transita de EmElaboracao para EmVotacao', function () {
        $admin = User::factory()->admin()->create();
        $processo = Processo::factory()->create([
            'id_administrador' => $admin->id,
            'status' => ProcessoStatus::EmElaboracao,
        ]);
        $fluxo = app(FluxoProcessual::class);

        $fluxo->abrirVotacao($processo);

        expect($processo->fresh()->status)->toBe(ProcessoStatus::EmVotacao);
    });

    test('encerrar rodada calcula resultado e transita para AguardandoMinerva', function () {
        $admin = User::factory()->admin()->create();
        $membro = User::factory()->create();
        $processo = Processo::factory()->create([
            'id_administrador' => $admin->id,
            'status' => ProcessoStatus::EmVotacao,
        ]);
        $etapa = $processo->etapas()->create([
            'tipo' => EtapaTipo::JuizoAdmissibilidade,
            'status' => ProcessoStatus::EmVotacao,
        ]);
        $rodada = $etapa->rodadasVotacao()->create([
            'data_abertura' => now(),
        ]);

        $fluxo = app(FluxoProcessual::class);
        $fluxo->encerrarRodada($rodada->fresh());

        $rodada->refresh();
        $processo->refresh();

        expect($rodada->data_encerramento)->not->toBeNull()
            ->and($processo->status)->toBe(ProcessoStatus::AguardandoMinerva);
    });

    test('registrar voto de minerva transita para VotacaoEncerrada', function () {
        $membro = User::factory()->create();
        $admin = User::factory()->admin()->create();
        $processo = Processo::factory()->create([
            'id_administrador' => $admin->id,
            'status' => ProcessoStatus::AguardandoMinerva,
        ]);
        $etapa = $processo->etapas()->create([
            'tipo' => EtapaTipo::JuizoAdmissibilidade,
            'status' => ProcessoStatus::AguardandoMinerva,
        ]);
        $rodada = $etapa->rodadasVotacao()->create([
            'data_abertura' => now(),
            'presidente_id' => $membro->id,
        ]);

        $fluxo = app(FluxoProcessual::class);
        $fluxo->registrarVotoMinerva($rodada);

        expect($processo->fresh()->status)->toBe(ProcessoStatus::VotacaoEncerrada);
    });

    test('votacao reprovada retorna processo para EmElaboracao', function () {
        $admin = User::factory()->admin()->create();
        $membro = User::factory()->create();
        $processo = Processo::factory()->create([
            'id_administrador' => $admin->id,
            'status' => ProcessoStatus::EmVotacao,
        ]);
        $etapa = $processo->etapas()->create([
            'tipo' => EtapaTipo::JuizoAdmissibilidade,
            'status' => ProcessoStatus::EmVotacao,
        ]);
        $rodada = $etapa->rodadasVotacao()->create([
            'data_abertura' => now(),
        ]);

        $fluxo = app(FluxoProcessual::class);
        $rodada->update(['resultado' => ResultadoVotacao::Reprovado]);
        $fluxo->processarResultadoVotacao($rodada);

        expect($processo->fresh()->status)->toBe(ProcessoStatus::EmElaboracao);
    });
});

describe('retorno ao secretario geral apos condicao ACPP/PAE no documento', function () {
    test('documento com condicao ACPP transita para AguardandoDevolucaoSecretaria', function () {
        $admin = User::factory()->admin()->create();
        $membro = User::factory()->create();
        $processo = Processo::factory()->create([
            'id_administrador' => $admin->id,
            'status' => ProcessoStatus::VotacaoEncerrada,
        ]);
        $etapa = $processo->etapas()->create([
            'tipo' => EtapaTipo::Relatoria,
            'status' => ProcessoStatus::VotacaoEncerrada,
        ]);
        $rodada = $etapa->rodadasVotacao()->create([
            'data_abertura' => now(),
            'data_encerramento' => now(),
            'resultado' => ResultadoVotacao::Aprovado,
            'presidente_id' => $membro->id,
        ]);

        $documento = $etapa->documentos()->create([
            'tipo' => DocumentoTipo::Relatorio,
            'condicao' => DocumentoCondicao::Acpp,
            'data_insercao' => now(),
            'anexo' => 'documentos/teste.pdf',
        ]);

        $fluxo = app(FluxoProcessual::class);
        $fluxo->verificarCondicaoDocumento($processo->fresh());

        expect($processo->fresh()->status)
            ->toBe(ProcessoStatus::AguardandoDevolucaoSecretaria);
    });

    test('documento com condicao PAE transita para AguardandoDevolucaoSecretaria', function () {
        $admin = User::factory()->admin()->create();
        $membro = User::factory()->create();
        $processo = Processo::factory()->create([
            'id_administrador' => $admin->id,
            'status' => ProcessoStatus::VotacaoEncerrada,
        ]);
        $etapa = $processo->etapas()->create([
            'tipo' => EtapaTipo::Relatoria,
            'status' => ProcessoStatus::VotacaoEncerrada,
        ]);
        $rodada = $etapa->rodadasVotacao()->create([
            'data_abertura' => now(),
            'data_encerramento' => now(),
            'resultado' => ResultadoVotacao::Aprovado,
            'presidente_id' => $membro->id,
        ]);

        $documento = $etapa->documentos()->create([
            'tipo' => DocumentoTipo::Diligencia,
            'condicao' => DocumentoCondicao::Pae,
            'data_insercao' => now(),
            'anexo' => 'documentos/teste.pdf',
        ]);

        $fluxo = app(FluxoProcessual::class);
        $fluxo->verificarCondicaoDocumento($processo->fresh());

        expect($processo->fresh()->status)
            ->toBe(ProcessoStatus::AguardandoDevolucaoSecretaria);
    });

    test('notificacao criada para o secretario geral ao devolver', function () {
        $admin = User::factory()->admin()->create();
        $membro = User::factory()->create();
        $processo = Processo::factory()->create([
            'id_administrador' => $admin->id,
            'status' => ProcessoStatus::VotacaoEncerrada,
        ]);
        $etapa = $processo->etapas()->create([
            'tipo' => EtapaTipo::Relatoria,
            'status' => ProcessoStatus::VotacaoEncerrada,
        ]);
        $rodada = $etapa->rodadasVotacao()->create([
            'data_abertura' => now(),
            'data_encerramento' => now(),
            'resultado' => ResultadoVotacao::Aprovado,
            'presidente_id' => $membro->id,
        ]);

        $documento = $etapa->documentos()->create([
            'tipo' => DocumentoTipo::Relatorio,
            'condicao' => DocumentoCondicao::Acpp,
            'data_insercao' => now(),
            'anexo' => 'documentos/teste.pdf',
        ]);

        $fluxo = app(FluxoProcessual::class);
        $fluxo->verificarCondicaoDocumento($processo->fresh());

        $notificacao = Notificacao::query()
            ->where('user_id', $admin->id)
            ->where('notificavel_type', Processo::class)
            ->where('notificavel_id', $processo->id)
            ->first();

        expect($notificacao)->not->toBeNull()
            ->and($notificacao->titulo)->toBe('Processo aguardando devolucao')
            ->and($notificacao->lida)->toBeFalse();
    });
});

describe('arquivamento pelo secretario geral', function () {
    test('secretario geral arquiva processo em AguardandoDevolucaoSecretaria', function () {
        $admin = User::factory()->admin()->create();
        $processo = Processo::factory()->create([
            'id_administrador' => $admin->id,
            'status' => ProcessoStatus::AguardandoDevolucaoSecretaria,
        ]);

        $fluxo = app(FluxoProcessual::class);
        $fluxo->arquivar($processo);

        expect($processo->fresh()->status)->toBe(ProcessoStatus::Arquivado);
    });

    test('nao pode arquivar se nao estiver aguardando devolucao', function () {
        $admin = User::factory()->admin()->create();
        $processo = Processo::factory()->create([
            'id_administrador' => $admin->id,
            'status' => ProcessoStatus::EmElaboracao,
        ]);

        $fluxo = app(FluxoProcessual::class);
        $fluxo->arquivar($processo);
    })->throws(\DomainException::class);
});

describe('distribuicao automatica de relator', function () {
    test('distribuir relator retorna membro ativo com menor carga', function () {
        $admin = User::factory()->admin()->create();
        $membro1 = User::factory()->create();
        $membro2 = User::factory()->create();

        $processo1 = Processo::factory()->create([
            'id_administrador' => $admin->id,
            'id_relator' => $membro1->id,
            'status' => ProcessoStatus::EmElaboracao,
        ]);

        $service = app(\App\Services\DistribuicaoService::class);

        $relatorId = $service->distribuirRelator();

        expect($relatorId)->toBe($membro2->id);
    });

    test('distribuir relator exclui usuario informado', function () {
        $admin = User::factory()->admin()->create();
        $membro = User::factory()->create();

        $service = app(\App\Services\DistribuicaoService::class);

        $relatorId = $service->distribuirRelator($membro->id);

        expect($relatorId)->not->toBe($membro->id);
    });
});

describe('redistribuicao de processos ao transferir secretaria', function () {
    test('processos sao redistribuidos ao transferir secretaria', function () {
        $admin = User::factory()->admin()->create();
        $novoSecretario = User::factory()->create();
        $membro = User::factory()->create();

        $processo1 = Processo::factory()->create([
            'id_administrador' => $admin->id,
            'id_relator' => $membro->id,
            'status' => ProcessoStatus::EmElaboracao,
        ]);

        $service = app(\App\Services\DistribuicaoService::class);
        $service->redistribuirProcessos($admin, $novoSecretario);

        $processo1->refresh();
        expect($processo1->id_administrador)->toBe($novoSecretario->id)
            ->and($processo1->id_relator)->toBe($membro->id);
    });
});