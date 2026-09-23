<?php

namespace App\Services;

use App\Models\Etapa;
use App\Models\Processo;
use App\Models\RodadaVotacao;
use App\Models\UsuarioMembro;

class MembroService
{
    public function __construct(
        private RelatorService $relatorService,
        private NotificacaoService $notificacoes,
    ) {
    }

    /**
     * Exclui o membro definitivamente: redistribui as relatorias ativas, redesigna as
     * rodadas de votacao abertas cujo presidente era ele e remove o registro.
     */
    public function excluir(UsuarioMembro $membro): void
    {
        $this->redistribuirRelatorias($membro);

        $this->redesignarRodadasPendentes($membro);

        $membro->delete();
    }

    /**
     * Redistribui os processos ativos do membro excluido para o proximo relator
     * disponivel, notificando cada novo relator.
     */
    public function redistribuirRelatorias(UsuarioMembro $membro): void
    {
        $processos = Processo::query()
            ->where('id_relator', $membro->siape)
            ->get();

        foreach ($processos as $processo) {
            $etapaAtual = $processo->etapa_atual;

            if (! $etapaAtual
                || $etapaAtual->status === Etapa::STATUS_ARQUIVADO
                || $etapaAtual->status === Etapa::STATUS_FINALIZADO
            ) {
                continue;
            }

            $novoRelator = $this->relatorService->proximoRelator($membro->siape);

            if (! $novoRelator) {
                continue;
            }

            $processo->update(['id_relator' => $novoRelator->siape]);

            $this->notificacoes->notificarProcessoDesignado($novoRelator, $processo);
        }
    }

    /**
     * Redesigna as rodadas de votacao abertas cujo presidente era o membro excluido
     * para outro membro ativo, notificando o novo presidente de cada rodada.
     */
    public function redesignarRodadasPendentes(UsuarioMembro $excluido): void
    {
        $rodadas = RodadaVotacao::query()
            ->where('id_presidente', $excluido->siape)
            ->whereNull('resultado')
            ->get();

        foreach ($rodadas as $rodada) {
            $novo = UsuarioMembro::query()
                ->whereNotNull('data_ativacao')
                ->where('siape', '!=', $excluido->siape)
                ->inRandomOrder()
                ->first();

            if (! $novo) {
                continue;
            }

            $rodada->update(['id_presidente' => $novo->siape]);

            $this->notificacoes->notificarNovoPresidente($novo->email, $novo->nome);
        }
    }
}