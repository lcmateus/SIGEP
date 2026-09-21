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
        |--------------------------------------------------------------------------
        | ETAPA ATUAL DE CADA PROCESSO
        |--------------------------------------------------------------------------
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
        | PROCESSOS FINALIZADOS
        |--------------------------------------------------------------------------
        */

        $processosFinalizados = $processosComEtapaAtual
            ->filter(function ($processo) {
                return $processo->etapa_atual_relatorio?->status === Etapa::STATUS_FINALIZADO;
            })
            ->count();

        /*
        |--------------------------------------------------------------------------
        | PROCESSOS POR STATUS
        |--------------------------------------------------------------------------
        */

        $processosPorStatus = collect([
            'Em Elaboração' => 0,
            'Em Votação' => 0,
            'Aguardando Minerva' => 0,
            'Devolvido' => 0,
            'Arquivado' => 0,
            'Finalizado' => 0,
        ]);

        foreach ($processos as $processo) {
            $etapaAtual = $processo->etapas
                ->sortByDesc('ordem')
                ->first(
                    fn ($etapa) =>
                    $etapa->status !== Etapa::STATUS_FINALIZADO
                )
                ?? $processo->etapas
                    ->sortByDesc('ordem')
                    ->first();

            if (
                $etapaAtual &&
                $processosPorStatus->has($etapaAtual->status)
            ) {
                $status = $etapaAtual->status;
                $processosPorStatus[$status] =
                    $processosPorStatus[$status] + 1;
            }
        }

        $processosPorStatus = $processosPorStatus->filter(
            fn ($quantidade) => $quantidade > 0
        );

        /*
        |--------------------------------------------------------------------------
        | PROCESSOS EM ANDAMENTO
        |--------------------------------------------------------------------------
        */

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

        /*
        |--------------------------------------------------------------------------
        | PROCESSOS DEVOLVIDOS
        |--------------------------------------------------------------------------
        */

        $processosDevolvidos = $processosComEtapaAtual
            ->filter(
                fn ($processo) =>
                $processo->etapa_atual_relatorio?->status === Etapa::STATUS_DEVOLVIDO
            )
            ->count();

        /*
        |--------------------------------------------------------------------------
        | PROCESSOS ARQUIVADOS
        |--------------------------------------------------------------------------
        */

        $processosArquivados = $processosComEtapaAtual
            ->filter(
                fn ($processo) =>
                $processo->etapa_atual_relatorio?->status === Etapa::STATUS_ARQUIVADO
            )
            ->count();

        /*
        |--------------------------------------------------------------------------
        | PROCESSOS SEM ETAPA
        |--------------------------------------------------------------------------
        */

        $processosSemEtapa = $processosComEtapaAtual
            ->filter(
                fn ($processo) =>
                !$processo->etapa_atual_relatorio
            )
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
        */

        $rodadas = RodadaVotacao::query()
            ->whereBetween('data_abertura', [
                $inicio->copy()->startOfDay(),
                $fim->copy()->endOfDay(),
            ])
            ->get();

        $totalVotacoes = $rodadas->count();

        $votacoesAbertas = $rodadas
            ->filter(
                fn ($rodada) =>
                is_null($rodada->data_encerramento)
            )
            ->count();

        $votacoesEncerradas = $rodadas
            ->filter(
                fn ($rodada) =>
                !is_null($rodada->data_encerramento)
            )
            ->count();

        $votacoesAprovadas = $rodadas
            ->filter(
                fn ($rodada) =>
                $rodada->resultado === 'aprovado'
            )
            ->count();

        $votacoesReprovadas = $rodadas
            ->filter(
                fn ($rodada) =>
                $rodada->resultado === 'reprovado'
            )
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
        |--------------------------------------------------------------------------
        | ATENÇÃO:
        | O banco utiliza "resalva" nessa opção.
        |--------------------------------------------------------------------------
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
        | EVOLUÇÃO
        |--------------------------------------------------------------------------
        */

        $evolucao = $this->evolucao(
            $inicio,
            $fim
        );

        /*
        |--------------------------------------------------------------------------
        | RETORNO DA VIEW
        |--------------------------------------------------------------------------
        */

        return view(
            'resultados.results',
            compact(
                'inicio',
                'fim',
                'periodo',
                'periodoLabel',

                'processos',
                'totalProcessos',
                'processosComEtapaAtual',
                'processosFinalizados',
                'processosPorStatus',
                'processosEmAndamento',
                'processosDevolvidos',
                'processosArquivados',
                'processosSemEtapa',
                'taxaConclusao',
                'processosPorTipo',

                'rodadas',
                'totalVotacoes',
                'votacoesAbertas',
                'votacoesEncerradas',
                'votacoesAprovadas',
                'votacoesReprovadas',
                'resultadoVotos',

                'votos',
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

                'evolucao'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | EVOLUÇÃO DOS PROCESSOS
    |--------------------------------------------------------------------------
    */

    private function evolucao(Carbon $inicio, Carbon $fim): array
    {
        $dados = [];

        $periodoInicio = $inicio->copy()->startOfMonth();
        $periodoFim = $fim->copy()->endOfMonth();

        while ($periodoInicio->lessThanOrEqualTo($periodoFim)) {
            $inicioMes = $periodoInicio->copy()->startOfMonth();
            $fimMes = $periodoInicio->copy()->endOfMonth();

            if ($inicioMes->lt($inicio)) {
                $inicioMes = $inicio->copy()->startOfDay();
            }

            if ($fimMes->gt($fim)) {
                $fimMes = $fim->copy()->endOfDay();
            }

            $processos = Processo::query()
                ->whereBetween('data_admissao', [
                    $inicioMes->toDateString(),
                    $fimMes->toDateString(),
                ])
                ->count();

            $etapas = Etapa::query()
                ->whereBetween('created_at', [
                    $inicioMes,
                    $fimMes,
                ])
                ->count();

            $dados[] = [
                'periodo' => $periodoInicio->format('m/Y'),
                'processos' => $processos,
                'etapas' => $etapas,
            ];

            $periodoInicio->addMonth();
        }

        return $dados;
    }

    /*
    |--------------------------------------------------------------------------
    | DEFINIÇÃO DO PERÍODO
    |--------------------------------------------------------------------------
    */

    private function definirPeriodo(string $periodo): array
    {
        $fim = Carbon::now()->endOfDay();

        switch ($periodo) {
            case '7':
                $inicio = Carbon::now()
                    ->subDays(6)
                    ->startOfDay();

                $label = 'Últimos 7 dias';
                break;

            case '30':
                $inicio = Carbon::now()
                    ->subDays(29)
                    ->startOfDay();

                $label = 'Últimos 30 dias';
                break;

            case '90':
                $inicio = Carbon::now()
                    ->subDays(89)
                    ->startOfDay();

                $label = 'Últimos 90 dias';
                break;

            case '180':
                $inicio = Carbon::now()
                    ->subDays(179)
                    ->startOfDay();

                $label = 'Últimos 180 dias';
                break;

            case '365':
                $inicio = Carbon::now()
                    ->subDays(364)
                    ->startOfDay();

                $label = 'Últimos 365 dias';
                break;

            case 'ano':
                $inicio = Carbon::now()
                    ->startOfYear();

                $label = 'Este ano';
                break;

            case 'mes':
                $inicio = Carbon::now()
                    ->startOfMonth();

                $label = 'Este mês';
                break;

            default:
                $inicio = Carbon::now()
                    ->subDays(29)
                    ->startOfDay();

                $label = 'Últimos 30 dias';
                break;
        }

        return [
            $inicio,
            $fim,
            $label,
        ];
    }
}