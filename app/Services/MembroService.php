<?php

namespace App\Services;

use App\Models\Etapa;
use App\Models\Processo;
use App\Models\RodadaVotacao;
use App\Models\UsuarioMembro;
use Illuminate\Support\Facades\DB;

class MembroService
{
    public function __construct(
        private RelatorService $relatorService,
        private NotificacaoService $notificacoes,
    ) {}

    /**
     * Exclui o membro definitivamente: redistribui as relatorias ativas e remove o registro.
     *
     * O presidente atual nao pode ser excluido (validado no controller).
     */
    public function excluir(UsuarioMembro $membro): void
    {
        $this->redistribuirRelatorias($membro);

        $membro->delete();
    }

    /**
     * Elege o membro informado como unico presidente da plataforma e reassina
     * todas as rodadas de votacao em aberto para ele.
     */
    public function elegerPresidente(UsuarioMembro $membro): void
    {
        DB::transaction(function () use ($membro) {
            UsuarioMembro::query()
                ->where('is_presidente', true)
                ->update(['is_presidente' => false]);

            $membro->update(['is_presidente' => true]);

            RodadaVotacao::query()
                ->whereNull('resultado')
                ->update(['id_presidente' => $membro->siape]);
        });
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
}
