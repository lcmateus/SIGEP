<?php

namespace App\Services;

use App\Models\Etapa;
use App\Models\Processo;
use App\Models\RodadaVotacao;
use App\Models\UsuarioMembro;
use App\Models\Voto;

class VotacaoService
{
    public const AGUARDA_MINERVA = 'aguarda_minerva';
    public const APROVADO = 'aprovado';
    public const REPROVADO = 'reprovado';

    /**
     * Contagem dos votos da rodada, excluindo o voto de Minerva.
     *
     * @return array{aprova:int, desaprova:int, abstencoes:int}
     */
    public function contagemVotos(RodadaVotacao $rodada): array
    {
        $votos = $rodada->votos()->where('is_minerva', false)->get();

        $aprova = 0;
        $desaprova = 0;
        $abstencoes = 0;

        foreach ($votos as $voto) {
            match ($voto->opcao) {
                'aprova', 'aprova com resalva' => $aprova++,
                'desaprova' => $desaprova++,
                'abstenho' => $abstencoes++,
                default => null,
            };
        }

        return [
            'aprova' => $aprova,
            'desaprova' => $desaprova,
            'abstencoes' => $abstencoes,
        ];
    }

    /**
     * Decide o estado de uma rodada com base nos votos comuns.
     * Retorna APROVADO, REPROVADO ou AGUARDA_MINERVA (empate).
     */
    public function decidirPorMaioria(RodadaVotacao $rodada): string
    {
        $votos = $this->contagemVotos($rodada);

        if ($votos['aprova'] === $votos['desaprova']) {
            return self::AGUARDA_MINERVA;
        }

        return $votos['aprova'] > $votos['desaprova']
            ? self::APROVADO
            : self::REPROVADO;
    }

    /**
     * Realiza a apuracao final da rodada e atualiza a etapa/processo.
     *
     * Se houver empate, a etapa vai para Aguardando Minerva e a rodada
     * permanece sem resultado aguardando o voto de desempate do presidente.
     *
     * @return string Estado decidido (APROVADO, REPROVADO ou AGUARDA_MINERVA)
     */
    public function apurar(RodadaVotacao $rodada): string
    {
        $decisao = $this->decidirPorMaioria($rodada);

        if ($decisao === self::AGUARDA_MINERVA) {
            $this->colocarAguardandoMinerva($rodada);
            return $decisao;
        }

        $this->finalizarRodada($rodada, $decisao);

        return $decisao;
    }

    /**
     * Aplica o voto de Minerva do presidente e finaliza a rodada conforme a escolha.
     */
    public function aplicarVotoMinerva(RodadaVotacao $rodada, string $opcao): string
    {
        $decisao = $opcao === 'aprova'
            ? self::APROVADO
            : self::REPROVADO;

        $this->finalizarRodada($rodada, $decisao);

        return $decisao;
    }

    /**
     * Grava o resultado e retorna o processo ao relator.
     *
     * A etapa atual e finalizada. A proxima etapa nao e criada automaticamente:
     * o relator escolhera (Acordo de Conduta ou Processo de Apuracao) numa tela
     * dedicada.
     */
    protected function finalizarRodada(RodadaVotacao $rodada, string $decisao): void
    {
        $rodada->update(['resultado' => $decisao]);

        $etapa = $rodada->etapa;

        if ($etapa) {
            $etapa->update(['status' => Etapa::STATUS_FINALIZADO]);
        }

        app(NotificacaoService::class)->notificarVotacaoEncerrada($rodada, $decisao);
    }

    /**
     * Coloca a etapa em Aguardando Minerva (empate).
     */
    protected function colocarAguardandoMinerva(RodadaVotacao $rodada): void
    {
        $etapa = $rodada->etapa;

        if ($etapa) {
            $etapa->update(['status' => Etapa::STATUS_AGUARDANDO_MINERVA]);
        }
    }

    /**
     * Retorna o presidente ativo (is_presidente = true).
     */
    public function presidenteAtual(): ?UsuarioMembro
    {
        return UsuarioMembro::query()
            ->where('is_presidente', true)
            ->whereNotNull('data_ativacao')
            ->first();
    }

    /**
     * Total de membros ativos aptos a votar.
     */
    public function totalMembrosAtivos(): int
    {
        return UsuarioMembro::query()
            ->whereNotNull('data_ativacao')
            ->count();
    }

    /**
     * Verifica se todos os membros ativos da plataforma ja votaram na rodada.
     * Votos de Minerva nao contam para essa verificacao.
     */
    public function todosMembrosVotaram(RodadaVotacao $rodada): bool
    {
        $votantes = $rodada->votos()
            ->where('is_minerva', false)
            ->distinct()
            ->pluck('id_membro');

        return $this->totalMembrosAtivos() > 0
            && $votantes->count() === $this->totalMembrosAtivos();
    }

    /**
     * Se todos os membros ativos ja votaram, apura a rodada automaticamente.
     *
     * @return string|null Estado decidido (APROVADO, REPROVADO ou AGUARDA_MINERVA),
     *                      ou null quando ainda faltam votos.
     */
    public function apurarSeTodosVotaram(RodadaVotacao $rodada): ?string
    {
        if (!$this->todosMembrosVotaram($rodada)) {
            return null;
        }

        return $this->apurar($rodada);
    }

    /**
     * Cria a proxima etapa escolhida pelo relator (Acordo de Conduta ou Processo de Apuracao).
     */
    public function criarProximaEtapa(Processo $processo, string $tipo): Etapa
    {
        $proximaOrdem = ($processo->etapas()->max('ordem') ?? 0) + 1;

        return Etapa::query()->create([
            'numero_sei_processo' => $processo->numero_sei,
            'ordem' => $proximaOrdem,
            'tipo' => $tipo,
            'status' => Etapa::STATUS_EM_ELABORACAO,
            'relatorio_texto' => null,
            'data_inicio' => now(),
            'data_envio_votacao' => null,
            'data_encerramento' => null,
        ]);
    }

    /**
     * Indica se o processo aguarda a decisao do relator sobre a proxima etapa
     * (ultima etapa finalizada e nenhuma em elaboracao pendente).
     */
    public function precisaEscolherProximaEtapa(Processo $processo): bool
    {
        $etapas = $processo->relationLoaded('etapas')
            ? $processo->etapas
            : $processo->etapas()->get();

        $ultima = $etapas->sortByDesc('ordem')->first();

        if (!$ultima || $ultima->status !== Etapa::STATUS_FINALIZADO) {
            return false;
        }

        return !$etapas->contains(fn ($etapa) => $etapa->status === Etapa::STATUS_EM_ELABORACAO);
    }
}
