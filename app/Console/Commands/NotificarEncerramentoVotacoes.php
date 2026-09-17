<?php

namespace App\Console\Commands;

use App\Models\RodadaVotacao;
use App\Services\NotificacaoService;
use Illuminate\Console\Command;

class NotificarEncerramentoVotacoes extends Command
{
    protected $signature = 'votacao:avisar-encerramento-24h';

    protected $description = 'Notifica por e-mail os membros que ainda nao votaram em votacoes que encerram nas proximas 24 horas';

    public function handle(NotificacaoService $service): int
    {
        $amanha = now()->addHours(24);

        $rodadas = RodadaVotacao::query()
            ->whereNull('resultado')
            ->whereNotNull('data_encerramento')
            ->where('data_encerramento', '>', now())
            ->where('data_encerramento', '<=', $amanha)
            ->with('etapa.processo')
            ->get();

        foreach ($rodadas as $rodada) {
            $service->notificarEncerramentoProximo($rodada);
        }

        $this->info("Lembretes enviados para {$rodadas->count()} votacao(oes).");

        return self::SUCCESS;
    }
}