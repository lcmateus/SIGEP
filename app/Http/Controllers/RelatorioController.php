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

        [$inicio, $fim, $nomePeriodo] = $this->definirPeriodo($periodo);

        /*
        |--------------------------------------------------------------------------
        | PROCESSOS
        |--------------------------------------------------------------------------
        */

        $processosQuery = Processo::query()
            ->whereBetween('data_admissao', [
                $inicio->toDateString(),
                $fim->toDateString(),
            ]);

        $totalProcessos = (clone $processosQuery)->count();

        $etapas = Etapa::query()
            ->whereHas('processo', function ($query) use ($inicio, $fim) {
                $query->whereBetween('data_admissao', [
                    $inicio->toDateString(),
                    $fim->toDateString(),
                ]);
            })
            ->get();

        $processosFinalizados = $etapas
            ->where('status', Etapa::STATUS_FINALIZADO)
            ->pluck('numero_sei_processo')
            ->unique()
            ->count();

        $processosDevolvidos = $etapas
            ->where('status', Etapa::STATUS_DEVOLVIDO)
            ->pluck('numero_sei_processo')
            ->unique()
            ->count();

        $processosArquivados = $etapas
            ->where('status', Etapa::STATUS_ARQUIVADO)
            ->pluck('numero_sei_processo')
            ->unique()
            ->count();

        $processosEmAndamento = $etapas
            ->whereIn('status', [
                Etapa::STATUS_EM_ELABORACAO,
                Etapa::STATUS_EM_VOTACAO,
                Etapa::STATUS_AGUARDANDO_MINERVA,
            ])
            ->pluck('numero_sei_processo')
            ->unique()
            ->count();

        $taxaConclusao = $totalProcessos > 0
            ? round(($processosFinalizados / $totalProcessos) * 100, 1)
            : 0;

        /*
        |--------------------------------------------------------------------------
        | PROCESSOS POR STATUS
        |--------------------------------------------------------------------------
        */

        $processosPorStatus = [
            Etapa::STATUS_EM_ELABORACAO => $etapas
                ->where('status', Etapa::STATUS_EM_ELABORACAO)
                ->pluck('numero_sei_processo')
                ->unique()
                ->count(),

            Etapa::STATUS_EM_VOTACAO => $etapas
                ->where('status', Etapa::STATUS_EM_VOTACAO)
                ->pluck('numero_sei_processo')
                ->unique()
                ->count(),

            Etapa::STATUS_AGUARDANDO_MINERVA => $etapas
                ->where('status', Etapa::STATUS_AGUARDANDO_MINERVA)
                ->pluck('numero_sei_processo')
                ->unique()
                ->count(),

            Etapa::STATUS_FINALIZADO => $processosFinalizados,

            Etapa::STATUS_DEVOLVIDO => $processosDevolvidos,

            Etapa::STATUS_ARQUIVADO => $processosArquivados,
        ];

        /*
        |--------------------------------------------------------------------------
        | PROCESSOS POR TIPO
        |--------------------------------------------------------------------------
        */

        $processosPorTipo = [];

        foreach (Etapa::TIPOS as $tipo) {
            $processosPorTipo[$tipo] = $etapas
                ->where('tipo', $tipo)
                ->pluck('numero_sei_processo')
                ->unique()
                ->count();
        }

        /*
        |--------------------------------------------------------------------------
        | VOTAÇÕES
        |--------------------------------------------------------------------------
        */

        $rodadasQuery = RodadaVotacao::query()
            ->whereHas('etapa.processo', function ($query) use ($inicio, $fim) {
                $query->whereBetween('data_admissao', [
                    $inicio->toDateString(),
                    $fim->toDateString(),
                ]);
            });

        $totalVotacoes = (clone $rodadasQuery)->count();

        $votacoesAbertas = (clone $rodadasQuery)
            ->whereNull('data_encerramento')
            ->count();

        $votacoesEncerradas = (clone $rodadasQuery)
            ->whereNotNull('data_encerramento')
            ->count();

        $votacoesAprovadas = (clone $rodadasQuery)
            ->where('resultado', 'aprovado')
            ->count();

        $votacoesReprovadas = (clone $rodadasQuery)
            ->where('resultado', 'reprovado')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | VOTOS
        |--------------------------------------------------------------------------
        */

        $votosQuery = Voto::query()
            ->whereHas('rodada.etapa.processo', function ($query) use ($inicio, $fim) {
                $query->whereBetween('data_admissao', [
                    $inicio->toDateString(),
                    $fim->toDateString(),
                ]);
            });

        $resultadoVotos = [
            'aprova' => (clone $votosQuery)
                ->where('opcao', 'aprova')
                ->count(),

            'desaprova' => (clone $votosQuery)
                ->where('opcao', 'desaprova')
                ->count(),

            'aprova com resalva' => (clone $votosQuery)
                ->where('opcao', 'aprova com resalva')
                ->count(),

            'abstenho' => (clone $votosQuery)
                ->where('opcao', 'abstenho')
                ->count(),
        ];

        $totalVotos = array_sum($resultadoVotos);

        /*
        |--------------------------------------------------------------------------
        | MEMBROS
        |--------------------------------------------------------------------------
        */

        $totalMembros = UsuarioMembro::query()->count();

        $membrosAtivos = UsuarioMembro::query()
            ->whereNotNull('data_ativacao')
            ->count();

        $totalAdministradores = UsuarioAdministrador::query()->count();

        $novosMembros = UsuarioMembro::query()
            ->whereBetween('data_ativacao', [
                $inicio,
                $fim,
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
            ->whereNotNull('data_ativacao')
            ->orderByDesc('processos_count')
            ->limit(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | EVOLUÇÃO MENSAL
        |--------------------------------------------------------------------------
        */

        $evolucao = $this->montarEvolucaoMensal($inicio, $fim);

        /*
        |--------------------------------------------------------------------------
        | PRIMEIRO E ÚLTIMO PROCESSO DO PERÍODO
        |--------------------------------------------------------------------------
        */

        $primeiroProcesso = (clone $processosQuery)
            ->orderBy('data_admissao')
            ->first();

        $ultimoProcesso = (clone $processosQuery)
            ->orderByDesc('data_admissao')
            ->first();

        /*
        |--------------------------------------------------------------------------
        | ANOS DISPONÍVEIS
        |--------------------------------------------------------------------------
        */

        $anosDisponiveis = Processo::query()
            ->selectRaw('YEAR(data_admissao) as ano')
            ->distinct()
            ->orderByDesc('ano')
            ->pluck('ano');

        /*
        |--------------------------------------------------------------------------
        | RESULTADOS
        |--------------------------------------------------------------------------
        */

        return view('resultados.results', compact(
            'periodo',
            'inicio',
            'fim',
            'nomePeriodo',

            'totalProcessos',
            'processosFinalizados',
            'processosEmAndamento',
            'processosDevolvidos',
            'processosArquivados',
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

            'totalMembros',
            'membrosAtivos',
            'totalAdministradores',
            'novosMembros',

            'rankingRelatores',

            'evolucao',

            'primeiroProcesso',
            'ultimoProcesso',

            'anosDisponiveis',
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | DEFINIR PERÍODO
    |--------------------------------------------------------------------------
    */

    private function definirPeriodo(string $periodo): array
    {
        $fim = Carbon::today();

        switch ($periodo) {

            case 'hoje':
                $inicio = Carbon::today();
                $nome = 'Hoje';
                break;

            case '15':
                $inicio = Carbon::today()->subDays(14);
                $nome = 'Últimos 15 dias';
                break;

            case '30':
                $inicio = Carbon::today()->subDays(29);
                $nome = 'Últimos 30 dias';
                break;

            case '3':
                $inicio = Carbon::today()->subMonths(3);
                $nome = 'Últimos 3 meses';
                break;

            case '6':
                $inicio = Carbon::today()->subMonths(6);
                $nome = 'Últimos 6 meses';
                break;

            case '12':
                $inicio = Carbon::today()->subYear();
                $nome = 'Último ano';
                break;

            default:

                if (preg_match('/^\d{4}$/', $periodo)) {

                    $ano = (int) $periodo;

                    $inicio = Carbon::create($ano, 1, 1)->startOfDay();

                    $fim = Carbon::create($ano, 12, 31)->endOfDay();

                    $nome = "Ano de {$ano}";

                } else {

                    $inicio = Carbon::today()->subDays(29);

                    $nome = 'Últimos 30 dias';
                }

                break;
        }

        return [
            $inicio,
            $fim,
            $nome,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | EVOLUÇÃO MENSAL
    |--------------------------------------------------------------------------
    */

    private function montarEvolucaoMensal(
        Carbon $inicio,
        Carbon $fim
    ): array {

        $meses = [];

        $cursor = $inicio->copy()->startOfMonth();

        while ($cursor <= $fim) {

            $inicioMes = $cursor->copy()->startOfMonth();

            $fimMes = $cursor->copy()->endOfMonth();

            if ($inicioMes->lt($inicio)) {
                $inicioMes = $inicio->copy();
            }

            if ($fimMes->gt($fim)) {
                $fimMes = $fim->copy();
            }

            /*
            |--------------------------------------------------------------------------
            | Processos criados
            |--------------------------------------------------------------------------
            */

            $criados = Processo::query()
                ->whereBetween('data_admissao', [
                    $inicioMes->toDateString(),
                    $fimMes->toDateString(),
                ])
                ->count();

            /*
            |--------------------------------------------------------------------------
            | Processos finalizados
            |--------------------------------------------------------------------------
            */

            $finalizados = Etapa::query()
                ->where('status', Etapa::STATUS_FINALIZADO)
                ->whereBetween('data_encerramento', [
                    $inicioMes,
                    $fimMes,
                ])
                ->pluck('numero_sei_processo')
                ->unique()
                ->count();

            /*
            |--------------------------------------------------------------------------
            | Processos devolvidos
            |--------------------------------------------------------------------------
            */

            $devolvidos = Etapa::query()
                ->where('status', Etapa::STATUS_DEVOLVIDO)
                ->whereBetween('updated_at', [
                    $inicioMes,
                    $fimMes,
                ])
                ->pluck('numero_sei_processo')
                ->unique()
                ->count();

            /*
            |--------------------------------------------------------------------------
            | Processos arquivados
            |--------------------------------------------------------------------------
            */

            $arquivados = Etapa::query()
                ->where('status', Etapa::STATUS_ARQUIVADO)
                ->whereBetween('updated_at', [
                    $inicioMes,
                    $fimMes,
                ])
                ->pluck('numero_sei_processo')
                ->unique()
                ->count();

            $meses[] = [
                'label' => $cursor->translatedFormat('M/y'),

                'criados' => $criados,

                'finalizados' => $finalizados,

                'devolvidos' => $devolvidos,

                'arquivados' => $arquivados,
            ];

            $cursor->addMonth();
        }

        return $meses;
    }
}