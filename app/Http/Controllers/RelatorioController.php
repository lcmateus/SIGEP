<?php

namespace App\Http\Controllers;

use App\Models\Etapa;
use App\Models\Processo;
use App\Models\RodadaVotacao;
use App\Models\UsuarioAdministrador;
use App\Models\UsuarioMembro;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RelatorioController extends Controller
{
    public function index(Request $request): View
    {
        $periodo = (string) $request->query('periodo', '30');

        [$inicio, $fim, $periodoLabel] = $this->definirPeriodo($periodo);

        $processos = Processo::query()
            ->with([
                'etapas' => fn ($query) => $query->orderByDesc('ordem'),
            ])
            ->whereBetween('data_admissao', [
                $inicio->toDateString(),
                $fim->toDateString(),
            ])
            ->get();

        $totalProcessos = $processos->count();

        $processosComEtapaAtual = $processos->map(function (Processo $processo) {
            $etapas = $processo->etapas;

            $etapaAtual = $etapas->first(
                fn (Etapa $etapa) => $etapa->status !== Etapa::STATUS_FINALIZADO
            ) ?? $etapas->first();

            $processo->setAttribute('etapa_atual_relatorio', $etapaAtual);

            return $processo;
        });

        $processosFinalizados = $processosComEtapaAtual
            ->filter(
                fn (Processo $processo) =>
                    $processo->etapa_atual_relatorio?->status === Etapa::STATUS_FINALIZADO
            )
            ->count();

        $processosEmAndamento = $processosComEtapaAtual
            ->filter(function (Processo $processo) {
                return in_array(
                    $processo->etapa_atual_relatorio?->status,
                    [
                        Etapa::STATUS_EM_ELABORACAO,
                        Etapa::STATUS_EM_VOTACAO,
                        Etapa::STATUS_AGUARDANDO_MINERVA,
                    ],
                    true
                );
            })
            ->count();

        $processosDevolvidos = $processosComEtapaAtual
            ->filter(
                fn (Processo $processo) =>
                    $processo->etapa_atual_relatorio?->status === Etapa::STATUS_DEVOLVIDO
            )
            ->count();

        $processosArquivados = $processosComEtapaAtual
            ->filter(
                fn (Processo $processo) =>
                    $processo->etapa_atual_relatorio?->status === Etapa::STATUS_ARQUIVADO
            )
            ->count();

        $processosSemEtapa = $processosComEtapaAtual
            ->filter(
                fn (Processo $processo) => $processo->etapa_atual_relatorio === null
            )
            ->count();

        $taxaConclusao = $totalProcessos > 0
            ? round(($processosFinalizados / $totalProcessos) * 100, 1)
            : 0;

        $processosPorStatus = collect([
            Etapa::STATUS_EM_ELABORACAO => 0,
            Etapa::STATUS_EM_VOTACAO => 0,
            Etapa::STATUS_AGUARDANDO_MINERVA => 0,
            Etapa::STATUS_DEVOLVIDO => 0,
            Etapa::STATUS_ARQUIVADO => 0,
            Etapa::STATUS_FINALIZADO => 0,
        ]);

        $processosPorTipo = collect([
            Etapa::TIPO_JUIZO => 0,
            Etapa::TIPO_PROCEDIMENTO_PRELIMINAR => 0,
            Etapa::TIPO_ACPP => 0,
            Etapa::TIPO_PAE => 0,
        ]);

        foreach ($processosComEtapaAtual as $processo) {
            $etapaAtual = $processo->etapa_atual_relatorio;

            if (! $etapaAtual) {
                continue;
            }

            if ($processosPorStatus->has($etapaAtual->status)) {
                $processosPorStatus->put(
                    $etapaAtual->status,
                    $processosPorStatus->get($etapaAtual->status, 0) + 1
                );
            }

            if ($processosPorTipo->has($etapaAtual->tipo)) {
                $processosPorTipo->put(
                    $etapaAtual->tipo,
                    $processosPorTipo->get($etapaAtual->tipo, 0) + 1
                );
            }
        }

        $processosPorStatus = $processosPorStatus
            ->filter(fn ($quantidade) => $quantidade > 0);

        $processosPorTipo = $processosPorTipo
            ->filter(fn ($quantidade) => $quantidade > 0);

        $rodadas = RodadaVotacao::query()
            ->with('votos')
            ->whereBetween('data_abertura', [
                $inicio->copy()->startOfDay(),
                $fim->copy()->endOfDay(),
            ])
            ->get();

        $totalVotacoes = $rodadas->count();

        $votacoesAbertas = $rodadas
            ->filter(fn (RodadaVotacao $rodada) => $rodada->data_encerramento === null)
            ->count();

        $votacoesEncerradas = $rodadas
            ->filter(fn (RodadaVotacao $rodada) => $rodada->data_encerramento !== null)
            ->count();

        $votacoesAprovadas = $rodadas
            ->filter(fn (RodadaVotacao $rodada) => $rodada->resultado === 'aprovado')
            ->count();

        $votacoesReprovadas = $rodadas
            ->filter(fn (RodadaVotacao $rodada) => $rodada->resultado === 'reprovado')
            ->count();

        $resultadoVotos = collect([
            'Aprovadas' => $votacoesAprovadas,
            'Reprovadas' => $votacoesReprovadas,
        ]);

        $votos = $rodadas->flatMap(
            fn (RodadaVotacao $rodada) => $rodada->votos
        );

        $totalVotos = $votos->count();

        $votosAprova = $votos
            ->where('opcao', 'aprova')
            ->count();

        $votosDesaprova = $votos
            ->whereIn('opcao', ['desaprova', 'desaprova com resalva'])
            ->count();

        $votosRessalva = $votos
            ->where('opcao', 'aprova com resalva')
            ->count();

        $votosAbstencao = $votos
            ->where('opcao', 'abstenho')
            ->count();

        $totalAdministradores = UsuarioAdministrador::query()->count();
        $totalMembros = UsuarioMembro::query()->count();

        $membrosAtivos = UsuarioMembro::query()
            ->whereNotNull('data_ativacao')
            ->count();

        $novosMembros = UsuarioMembro::query()
            ->whereBetween('data_ativacao', [
                $inicio->copy()->startOfDay(),
                $fim->copy()->endOfDay(),
            ])
            ->count();

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

        $evolucao = $this->montarEvolucao($inicio, $fim);

        $anosDisponiveis = Processo::query()
            ->selectRaw('YEAR(data_admissao) as ano')
            ->whereNotNull('data_admissao')
            ->distinct()
            ->orderByDesc('ano')
            ->pluck('ano');

        return view('resultados.results', compact(
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
            'evolucao',
            'anosDisponiveis'
        ));
    }

    public function pdf(Request $request)
    {
        $dados = $this->index($request)->getData();

        $nomePeriodo = Str::slug($dados['periodoLabel'] ?? 'relatorio');

        return Pdf::loadView('resultados.pdf', $dados)
            ->setPaper('a4', 'portrait')
            ->download("sigep-resultados-{$nomePeriodo}.pdf");
    }

    private function montarEvolucao(Carbon $inicio, Carbon $fim): array
    {
        $evolucao = [
            'labels' => [],
            'entraram' => [],
            'finalizados' => [],
            'devolvidos' => [],
        ];

        $cursor = $inicio->copy()->startOfMonth();

        while ($cursor->lessThanOrEqualTo($fim)) {
            $inicioMes = $cursor->copy()->startOfMonth();
            $fimMes = $cursor->copy()->endOfMonth();

            if ($inicioMes->lt($inicio)) {
                $inicioMes = $inicio->copy();
            }

            if ($fimMes->gt($fim)) {
                $fimMes = $fim->copy();
            }

            $evolucao['labels'][] = $cursor->translatedFormat('M/Y');

            $evolucao['entraram'][] = Processo::query()
                ->whereBetween('data_admissao', [
                    $inicioMes->toDateString(),
                    $fimMes->toDateString(),
                ])
                ->count();

            $evolucao['finalizados'][] = Etapa::query()
                ->where('status', Etapa::STATUS_FINALIZADO)
                ->whereBetween('data_encerramento', [$inicioMes, $fimMes])
                ->count();

            $evolucao['devolvidos'][] = Etapa::query()
                ->where('status', Etapa::STATUS_DEVOLVIDO)
                ->whereBetween('updated_at', [$inicioMes, $fimMes])
                ->count();

            $cursor->addMonth();
        }

        return $evolucao;
    }

    private function definirPeriodo(string $periodo): array
    {
        $hoje = Carbon::today();
        $fim = $hoje->copy()->endOfDay();

        switch ($periodo) {
            case 'hoje':
                $inicio = $hoje->copy()->startOfDay();
                $label = 'Hoje';
                break;

            case '15':
                $inicio = $hoje->copy()->subDays(14)->startOfDay();
                $label = 'Últimos 15 dias';
                break;

            case '30':
                $inicio = $hoje->copy()->subDays(29)->startOfDay();
                $label = 'Últimos 30 dias';
                break;

            case '3':
                $inicio = $hoje->copy()->subMonths(3)->startOfDay();
                $label = 'Últimos 3 meses';
                break;

            case '6':
                $inicio = $hoje->copy()->subMonths(6)->startOfDay();
                $label = 'Últimos 6 meses';
                break;

            case '12':
                $inicio = $hoje->copy()->subYear()->startOfDay();
                $label = 'Último ano';
                break;

            default:
                if (preg_match('/^\d{4}$/', $periodo) === 1) {
                    $ano = (int) $periodo;
                    $inicio = Carbon::create($ano, 1, 1)->startOfDay();
                    $fim = Carbon::create($ano, 12, 31)->endOfDay();
                    $label = "Ano de {$ano}";
                } else {
                    $inicio = $hoje->copy()->subDays(29)->startOfDay();
                    $label = 'Últimos 30 dias';
                    $periodo = '30';
                }

                break;
        }

        return [$inicio, $fim, $label];
    }
}