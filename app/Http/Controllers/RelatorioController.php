<?php

namespace App\Http\Controllers;

use App\Models\Etapa;
use App\Models\Processo;
use App\Models\RodadaVotacao;
use App\Models\UsuarioAdministrador;
use App\Models\UsuarioMembro;
use App\Models\Voto;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RelatorioController extends Controller
{
    public function index(Request $request): View
    {
        $periodo = $request->get('periodo', '30');

        [$inicio, $fim, $periodoLabel] = $this->definirPeriodo($periodo);

        /*
        |--------------------------------------------------------------------------
        | PROCESSOS
        |--------------------------------------------------------------------------
        |
        | Os indicadores de processos consideram a data de admissão.
        | Cada processo é contado apenas uma vez.
        |
        */

        $processos = Processo::query()
            ->with('etapas')
            ->whereBetween('data_admissao', [
                $inicio->toDateString(),
                $fim->toDateString(),
            ])
            ->get();

        $totalProcessos = $processos->count();

        /*
         * Descobre a etapa atual de cada processo.
         *
         * A regra é a mesma utilizada no model Processo:
         * maior ordem que ainda não esteja finalizada.
         * Caso todas estejam finalizadas, utiliza a última etapa.
         */
        $processosComEtapaAtual = $processos->map(function ($processo) {
            $etapas = $processo->etapas->sortByDesc('ordem');

            $etapaAtual = $etapas
                ->first(fn ($etapa) => $etapa->status !== Etapa::STATUS_FINALIZADO);

            $etapaAtual ??= $etapas->first();

            $processo->etapa_atual_relatorio = $etapaAtual;

            return $processo;
        });

        /*
        |--------------------------------------------------------------------------
        | STATUS ATUAL DOS PROCESSOS
        |--------------------------------------------------------------------------
        */

        $processosPorStatus = collect([
            Etapa::STATUS_EM_ELABORACAO => 0,
            Etapa::STATUS_EM_VOTACAO => 0,
            Etapa::STATUS_AGUARDANDO_MINERVA => 0,
            Etapa::STATUS_DEVOLVIDO => 0,
            Etapa::STATUS_ARQUIVADO => 0,
            Etapa::STATUS_FINALIZADO => 0,
        ]);

        foreach ($processosComEtapaAtual as $processo) {
            $status = $processo->etapa_atual_relatorio?->status;

            if ($status && $processosPorStatus->has($status)) {
                $processosPorStatus[$status]++;
            }
        }

        $processosPorStatus = $processosPorStatus->filter(
            fn ($quantidade) => $quantidade > 0
        );

        $processosFinalizados = $processosComEtapaAtual
            ->filter(fn ($processo) =>
                $processo->etapa_atual_relatorio?->status === Etapa::STATUS_FINALIZADO
            )
            ->count();

        $processosEmAndamento = $processosComEtapaAtual
            ->filter(function ($processo) {
                $status = $processo->etapa_atual_relatorio?->status;

                return in_array($status, [
                    Etapa::STATUS_EM_ELABORACAO,
                    Etapa::STATUS_EM_VOTACAO,
                    Etapa::STATUS_AGUARDANDO_MINERVA,
                ], true);
            })
            ->count();

        $processosDevolvidos = $processosComEtapaAtual
            ->filter(fn ($processo) =>
                $processo->etapa_atual_relatorio?->status === Etapa::STATUS_DEVOLVIDO
            )
            ->count();

        $processosArquivados = $processosComEtapaAtual
            ->filter(fn ($processo) =>
                $processo->etapa_atual_relatorio?->status === Etapa::STATUS_ARQUIVADO
            )
            ->count();

        $processosSemEtapa = $processosComEtapaAtual
            ->filter(fn ($processo) => !$processo->etapa_atual_relatorio)
            ->count();

        /*
        |--------------------------------------------------------------------------
        | TAXA DE CONCLUSÃO
        |--------------------------------------------------------------------------
        */

        $taxaConclusao = $totalProcessos > 0
            ? round(($processosFinalizados / $totalProcessos) * 100, 1)
            : 0;

        /*
        |--------------------------------------------------------------------------
        | PROCESSOS POR TIPO DA ETAPA ATUAL
        |--------------------------------------------------------------------------
        */

        $processosPorTipo = collect([
            Etapa::TIPO_JUIZO => 0,
            Etapa::TIPO_PROCEDIMENTO_PRELIMINAR => 0,
            Etapa::TIPO_ACPP => 0,
            Etapa::TIPO_PAE => 0,
        ]);

        foreach ($processosComEtapaAtual as $processo) {
            $tipo = $processo->etapa_atual_relatorio?->tipo;

            if ($tipo && $processosPorTipo->has($tipo)) {
                $processosPorTipo[$tipo]++;
            }
        }

        $processosPorTipo = $processosPorTipo->filter(
            fn ($quantidade) => $quantidade > 0
        );

        /*
        |--------------------------------------------------------------------------
        | VOTAÇÕES
        |--------------------------------------------------------------------------
        |
        | Votações são filtradas pela data de abertura da rodada.
        | Isso evita perder uma votação ocorrida no período em um processo
        | que foi admitido meses antes.
        |
        */

        $rodadas = RodadaVotacao::query()
            ->whereBetween('data_abertura', [
                $inicio->copy()->startOfDay(),
                $fim->copy()->endOfDay(),
            ])
            ->get();

        $totalVotacoes = $rodadas->count();

        $votacoesAbertas = $rodadas
            ->filter(fn ($rodada) => is_null($rodada->data_encerramento))
            ->count();

        $votacoesEncerradas = $rodadas
            ->filter(fn ($rodada) => !is_null($rodada->data_encerramento))
            ->count();

        $votacoesAprovadas = $rodadas
            ->filter(fn ($rodada) => $rodada->resultado === 'aprovado')
            ->count();

        $votacoesReprovadas = $rodadas
            ->filter(fn ($rodada) => $rodada->resultado === 'reprovado')
            ->count();

        $resultadoVotos = collect([
            'Aprovadas' => $votacoesAprovadas,
            'Reprovadas' => $votacoesReprovadas,
        ]);

        /*
        |--------------------------------------------------------------------------
        | VOTOS
        |--------------------------------------------------------------------------
        */

        $votos = Voto::query()
            ->whereHas('rodada', function ($query) use ($inicio, $fim) {
                $query->whereBetween('data_abertura', [
                    $inicio->copy()->startOfDay(),
                    $fim->copy()->endOfDay(),
                ]);
            })
            ->get();

        $totalVotos = $votos->count();

        $votosAprova = $votos
            ->where('opcao', 'aprova')
            ->count();

        $votosDesaprova = $votos
            ->where('opcao', 'desaprova')
            ->count();

        /*
         * Atenção:
         * o banco possui "resalva" sem o segundo S.
         * Mantemos esse valor aqui para compatibilidade com a migration.
         */
        $votosRessalva = $votos
            ->where('opcao', 'aprova com resalva')
            ->count();

        $votosAbstencao = $votos
            ->where('opcao', 'abstenho')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | USUÁRIOS
        |--------------------------------------------------------------------------
        */

        $totalAdministradores = UsuarioAdministrador::count();

        $totalMembros = UsuarioMembro::count();

        $membrosAtivos = UsuarioMembro::query()
            ->whereNotNull('data_ativacao')
            ->count();

        $novosMembros = UsuarioMembro::query()
            ->whereBetween('data_ativacao', [
                $inicio->copy()->startOfDay(),
                $fim->copy()->endOfDay(),
            ])
            ->count();

        /*
        |--------------------------------------------------------------------------
        | RANKING DE RELATORES
        |--------------------------------------------------------------------------
        */

        $rankingRelatores = UsuarioMembro::query()
            ->withCount([
                'processosComoRelator as processos_count' => function ($query) use ($inicio, $fim) {
                    $query->whereBetween('data_admissao', [
                        $inicio->toDateString(),
                        $fim->toDateString(),
                    ]);
                },
            ])
            ->orderByDesc('processos_count')
            ->limit(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | EVOLUÇÃO MENSAL
        |--------------------------------------------------------------------------
        */

        $evolucao = $this->montarEvolucaoMensal(
            $inicio,
            $fim
        );

        /*
        |--------------------------------------------------------------------------
        | ANOS DISPONÍVEIS
        |--------------------------------------------------------------------------
        */

        $anosDisponiveis = Processo::query()
            ->selectRaw('YEAR(data_admissao) as ano')
            ->whereNotNull('data_admissao')
            ->distinct()
            ->orderByDesc('ano')
            ->pluck('ano')
            ->map(fn ($ano) => (string) $ano)
            ->values();

        /*
        |--------------------------------------------------------------------------
        | LABEL DO PERÍODO
        |--------------------------------------------------------------------------
        */

        return view('resultados.results', compact(
            'periodo',
            'periodoLabel',
            'inicio',
            'fim',

            'totalProcessos',
            'processosFinalizados',
            'processosEmAndamento',
            'processosDevolvidos',
            'processosArquivados',
            'processosSemEtapa',
            'taxaConclusao',

            'processosPorStatus',
            'processosPorTipo',

            'totalVotacoes',
            'votacoesAbertas',
            'votacoesEncerradas',
            'votacoesAprovadas',
            'votacoesReprovadas',
            'resultadoVotos',

            'totalVotos',
            'votosAprova',
            'votosDesaprova',
            'votosRessalva',
            'votosAbstencao',

            'totalAdministradores',
            'totalMembros',
            'membrosAtivos',
            'novosMembros',

            'rankingRelatores',
            'evolucao',
            'anosDisponiveis'
        ));
    }

    /**
     * Define o intervalo utilizado pelos filtros do dashboard.
     */
    private function definirPeriodo(string $periodo): array
    {
        $agora = Carbon::now();

        /*
         * Filtro por ano.
         * Exemplo: ?periodo=2026
         */
        if (preg_match('/^\d{4}$/', $periodo)) {
            $ano = (int) $periodo;

            $inicio = Carbon::create($ano, 1, 1)->startOfDay();
            $fim = Carbon::create($ano, 12, 31)->endOfDay();

            return [
                $inicio,
                $fim,
                "Ano de {$ano}",
            ];
        }

        switch ($periodo) {
            case 'hoje':
                $inicio = $agora->copy()->startOfDay();
                $fim = $agora->copy()->endOfDay();
                $label = 'Hoje';
                break;

            case '15':
                $inicio = $agora->copy()->subDays(14)->startOfDay();
                $fim = $agora->copy()->endOfDay();
                $label = 'Últimos 15 dias';
                break;

            case '30':
                $inicio = $agora->copy()->subDays(29)->startOfDay();
                $fim = $agora->copy()->endOfDay();
                $label = 'Últimos 30 dias';
                break;

            case '3':
                $inicio = $agora->copy()->subMonths(3)->startOfDay();
                $fim = $agora->copy()->endOfDay();
                $label = 'Últimos 3 meses';
                break;

            case '6':
                $inicio = $agora->copy()->subMonths(6)->startOfDay();
                $fim = $agora->copy()->endOfDay();
                $label = 'Últimos 6 meses';
                break;

            case '12':
            case '1ano':
                $inicio = $agora->copy()->subYear()->startOfDay();
                $fim = $agora->copy()->endOfDay();
                $label = 'Último 1 ano';
                break;

            default:
                $inicio = $agora->copy()->subDays(29)->startOfDay();
                $fim = $agora->copy()->endOfDay();
                $label = 'Últimos 30 dias';
                break;
        }

        return [$inicio, $fim, $label];
    }

    /**
     * Monta os dados da evolução mensal.
     */
    private function montarEvolucaoMensal(
        Carbon $inicio,
        Carbon $fim
    ): array {
        /*
         * Se o período for muito curto, ainda mostramos os meses
         * envolvidos para o gráfico não ficar vazio.
         */
        $inicioMes = $inicio->copy()->startOfMonth();
        $fimMes = $fim->copy()->startOfMonth();

        $labels = [];
        $entraram = [];
        $finalizados = [];
        $devolvidos = [];
        $arquivados = [];

        $cursor = $inicioMes->copy();

        while ($cursor <= $fimMes) {
            $mesInicio = $cursor->copy()->startOfMonth();
            $mesFim = $cursor->copy()->endOfMonth();

            $labels[] = $cursor->translatedFormat('M/Y');

            $entraram[] = Processo::query()
                ->whereBetween('data_admissao', [
                    $mesInicio->toDateString(),
                    $mesFim->toDateString(),
                ])
                ->count();

            $finalizados[] = Etapa::query()
                ->where('status', Etapa::STATUS_FINALIZADO)
                ->whereNotNull('data_encerramento')
                ->whereBetween('data_encerramento', [
                    $mesInicio,
                    $mesFim,
                ])
                ->count();

            /*
             * O processo possui uma data específica para devolução.
             * Ela é mais confiável do que updated_at para representar
             * quando a devolução aconteceu.
             */
            $devolvidos[] = Processo::query()
                ->whereNotNull('data_devolucao')
                ->whereBetween('data_devolucao', [
                    $mesInicio->toDateString(),
                    $mesFim->toDateString(),
                ])
                ->count();

            /*
             * A tabela não possui data exclusiva de arquivamento.
             * Nesse caso usamos updated_at da etapa arquivada como
             * registro temporal disponível.
             */
            $arquivados[] = Etapa::query()
                ->where('status', Etapa::STATUS_ARQUIVADO)
                ->whereBetween('updated_at', [
                    $mesInicio,
                    $mesFim,
                ])
                ->count();

            $cursor->addMonth();
        }

        return [
            'labels' => $labels,
            'entraram' => $entraram,
            'finalizados' => $finalizados,
            'devolvidos' => $devolvidos,
            'arquivados' => $arquivados,
        ];
    }
}