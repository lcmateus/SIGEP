<?php

namespace App\Services;

use App\Enums\DocumentoCondicao;
use App\Enums\EtapaTipo;
use App\Enums\ProcessoStatus;
use App\Enums\ResultadoVotacao;
use App\Enums\UserTipo;
use App\Models\Etapa;
use App\Models\Notificacao;
use App\Models\Processo;
use App\Models\RodadaVotacao;
use App\Models\User;
use App\Enums\UserStatus;

class FluxoProcessual
{
    private function transicoes(): array
    {
        return [
            ProcessoStatus::EmElaboracao->value => [
                ProcessoStatus::EmVotacao,
            ],
            ProcessoStatus::EmVotacao->value => [
                ProcessoStatus::AguardandoMinerva,
                ProcessoStatus::VotacaoEncerrada,
                ProcessoStatus::EmElaboracao,
            ],
            ProcessoStatus::AguardandoMinerva->value => [
                ProcessoStatus::VotacaoEncerrada,
            ],
            ProcessoStatus::VotacaoEncerrada->value => [
                ProcessoStatus::Concluido,
                ProcessoStatus::AguardandoDevolucaoSecretaria,
                ProcessoStatus::EmElaboracao,
            ],
            ProcessoStatus::Concluido->value => [
                ProcessoStatus::AguardandoDevolucaoSecretaria,
            ],
            ProcessoStatus::AguardandoDevolucaoSecretaria->value => [
                ProcessoStatus::Arquivado,
            ],
            ProcessoStatus::Arquivado->value => [],
        ];
    }

    public function podeTransitar(Processo $processo, ProcessoStatus $destino): bool
    {
        $atuaisPermitidas = $this->transicoes()[$processo->status->value] ?? [];

        return in_array($destino, $atuaisPermitidas, true);
    }

    public function transitar(Processo $processo, ProcessoStatus $destino): Processo
    {
        if (! $this->podeTransitar($processo, $destino)) {
            throw new \DomainException(
                "Transicao invalida de {$processo->status->value} para {$destino->value}."
            );
        }

        $processo->update(['status' => $destino]);

        if ($destino === ProcessoStatus::AguardandoDevolucaoSecretaria) {
            $this->notificarSecretarioGeral($processo);
        }

        return $processo;
    }

    public function abrirVotacao(Processo $processo): Processo
    {
        return $this->transitar($processo, ProcessoStatus::EmVotacao);
    }

    public function encerrarRodada(RodadaVotacao $rodada): RodadaVotacao
    {
        $rodada->update([
            'data_encerramento' => now(),
            'resultado' => $this->calcularResultado($rodada),
        ]);

        $etapa = $rodada->etapa;
        $processo = $etapa->processo;

        if ($processo->status === ProcessoStatus::EmVotacao) {
            $this->transitar($processo, ProcessoStatus::AguardandoMinerva);
        }

        return $rodada;
    }

    public function registrarVotoMinerva(RodadaVotacao $rodada): RodadaVotacao
    {
        $processo = $rodada->etapa->processo;

        if ($processo->status === ProcessoStatus::AguardandoMinerva) {
            $this->transitar($processo, ProcessoStatus::VotacaoEncerrada);
        }

        return $rodada;
    }

    public function processarResultadoVotacao(RodadaVotacao $rodada): ?Processo
    {
        $processo = $rodada->etapa->processo;
        $resultado = $rodada->resultado;

        if ($resultado === ResultadoVotacao::Reprovado) {
            $this->transitar($processo, ProcessoStatus::EmElaboracao);
            return $processo;
        }

        if ($resultado === ResultadoVotacao::Aprovado) {
            return $this->avaliarConclusaoEtapa($rodada->etapa);
        }

        return null;
    }

    public function avaliarConclusaoEtapa(Etapa $etapa): ?Processo
    {
        $rodadas = $etapa->rodadasVotacao;
        $ultimaRodada = $rodadas->sortByDesc('id')->first();

        if (! $ultimaRodada || $ultimaRodada->resultado === ResultadoVotacao::Pendente) {
            return null;
        }

        $processo = $etapa->processo;

        if ($etapa->tipo === EtapaTipo::Arquivamento
            && $ultimaRodada->resultado === ResultadoVotacao::Aprovado) {
            $this->transitar($processo, ProcessoStatus::AguardandoDevolucaoSecretaria);

            return $processo;
        }

        $todasEtapasConcluidas = $processo->etapas()
            ->where('status', '!=', ProcessoStatus::VotacaoEncerrada->value)
            ->where('status', '!=', ProcessoStatus::Concluido->value)
            ->doesntExist();

        if ($todasEtapasConcluidas && $processo->status === ProcessoStatus::VotacaoEncerrada) {
            $etapa->update(['status' => ProcessoStatus::Concluido]);

            $this->transitar($processo, ProcessoStatus::Concluido);
        }

        return $processo;
    }

    public function verificarCondicaoDocumento(Processo $processo): ?Processo
    {
        $temCondicao = $processo->etapas()
            ->whereHas('documentos', function ($query) {
                $query->whereIn('condicao', [
                    DocumentoCondicao::Acpp->value,
                    DocumentoCondicao::Pae->value,
                ]);
            })
            ->exists();

        if ($temCondicao && $processo->status !== ProcessoStatus::AguardandoDevolucaoSecretaria) {
            $this->transitar($processo, ProcessoStatus::AguardandoDevolucaoSecretaria);
            return $processo;
        }

        return null;
    }

    public function arquivar(Processo $processo): Processo
    {
        return $this->transitar($processo, ProcessoStatus::Arquivado);
    }

    private function calcularResultado(RodadaVotacao $rodada): ResultadoVotacao
    {
        $votos = $rodada->votos;

        if ($votos->isEmpty()) {
            return ResultadoVotacao::Pendente;
        }

        $favor = $votos->where('opcao.value', 'favor')->count()
            + $votos->where('opcao.value', 'favor_com_ressalvas')->count();
        $contra = $votos->where('opcao.value', 'contra')->count();

        if ($favor > $contra) {
            return ResultadoVotacao::Aprovado;
        }

        return ResultadoVotacao::Reprovado;
    }

    private function notificarSecretarioGeral(Processo $processo): void
    {
        $secretarios = User::query()
            ->where('tipo', UserTipo::Admin)
            ->where('status', UserStatus::Ativo)
            ->get();

        foreach ($secretarios as $secretario) {
            Notificacao::query()->create([
                'user_id' => $secretario->id,
                'titulo' => 'Processo aguardando devolucao',
                'mensagem' => "O processo {$processo->numero_sei} foi concluido e aguarda arquivamento.",
                'tipo' => 'processo',
                'notificavel_type' => Processo::class,
                'notificavel_id' => $processo->id,
            ]);
        }
    }
}
