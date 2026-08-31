<?php

namespace App\Console\Commands;

use App\Models\Etapa;
use App\Models\RodadaVotacao;
use App\Services\VotacaoService;
use Illuminate\Console\Command;

class FecharVotacoes extends Command
{
    protected $signature = 'votacao:fechar-vencidas';

    protected $description = 'Apura rodadas de votacao cuja data de encerramento ja venceu';

    public function handle(VotacaoService $service): int
    {
        $rodadas = RodadaVotacao::query()
            ->whereNull('resultado')
            ->whereNotNull('data_encerramento')
            ->where('data_encerramento', '<=', now())
            ->with('etapa')
            ->get();

        $aprovadas = 0;
        $reprovadas = 0;
        $aguardando = 0;

        foreach ($rodadas as $rodada) {
            $decisao = $service->apurar($rodada);

            match ($decisao) {
                VotacaoService::APROVADO => $aprovadas++,
                VotacaoService::REPROVADO => $reprovadas++,
                default => $aguardando++,
            };
        }

        $this->info("Votacoes apuradas: {$aprovadas} aprovada(s), {$reprovadas} reprovada(s), {$aguardando} aguardando Minerva.");

        return self::SUCCESS;
    }
}
