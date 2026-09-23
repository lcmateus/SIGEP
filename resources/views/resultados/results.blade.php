@extends('layouts.main_layout')

@section('content')

<div class="space-y-8">

    {{-- ============================================================
         CABEÇALHO
    ============================================================= --}}
    <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">

        <div>
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-100">
                    <svg class="h-6 w-6 text-emerald-700"
                         fill="none"
                         stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>

                <div>
                    <h1 class="text-2xl font-bold text-slate-800">
                        Resultados
                    </h1>

                    <p class="text-sm text-slate-500">
                        Painel de indicadores e estatísticas do SIGEP
                    </p>
                </div>
            </div>
        </div>

        {{-- FILTRO --}}
        <form method="GET"
              action="{{ route('resultados') }}"
              class="flex flex-col gap-2 sm:flex-row sm:items-center">

            <label for="periodo"
                   class="text-sm font-medium text-slate-600">
                Período
            </label>

            <select
                id="periodo"
                name="periodo"
                onchange="this.form.submit()"
                class="min-w-[190px] rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 shadow-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">

                <option value="hoje"
                    {{ $periodo === 'hoje' ? 'selected' : '' }}>
                    Hoje
                </option>

                <option value="15"
                    {{ $periodo === '15' ? 'selected' : '' }}>
                    Últimos 15 dias
                </option>

                <option value="30"
                    {{ $periodo === '30' ? 'selected' : '' }}>
                    Últimos 30 dias
                </option>

                <option value="3"
                    {{ $periodo === '3' ? 'selected' : '' }}>
                    Últimos 3 meses
                </option>

                <option value="6"
                    {{ $periodo === '6' ? 'selected' : '' }}>
                    Últimos 6 meses
                </option>

                <option value="12"
                    {{ $periodo === '12' ? 'selected' : '' }}>
                    Último 1 ano
                </option>

                @if(isset($anosDisponiveis) && $anosDisponiveis->count())
                    <optgroup label="Anos">

                        @foreach($anosDisponiveis as $ano)
                            <option value="{{ $ano }}"
                                {{ $periodo === (string) $ano ? 'selected' : '' }}>
                                {{ $ano }}
                            </option>
                        @endforeach

                    </optgroup>
                @endif

            </select>
        </form>
    </div>


    {{-- ============================================================
         PERÍODO ATUAL
    ============================================================= --}}
    <div class="flex items-center gap-3 rounded-xl border border-emerald-100 bg-emerald-50 px-5 py-4">

        <svg class="h-5 w-5 shrink-0 text-emerald-600"
             fill="none"
             stroke="currentColor"
             viewBox="0 0 24 24">
            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>

        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-emerald-600">
                Período selecionado
            </p>

            <p class="text-sm font-semibold text-emerald-900">
                {{ $periodoLabel ?? 'Últimos 30 dias' }}
            </p>
        </div>
    </div>


    {{-- ============================================================
         INDICADORES PRINCIPAIS
    ============================================================= --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">

        {{-- Total --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-start justify-between">

                <div>
                    <p class="text-sm font-medium text-slate-500">
                        Processos recebidos
                    </p>

                    <p class="mt-3 text-3xl font-bold text-slate-800">
                        {{ $totalProcessos }}
                    </p>

                    <p class="mt-1 text-xs text-slate-400">
                        No período selecionado
                    </p>
                </div>

                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-100">
                    <svg class="h-6 w-6 text-emerald-700"
                         fill="none"
                         stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h6l5 5v11a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
        </div>


        {{-- Em andamento --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-start justify-between">

                <div>
                    <p class="text-sm font-medium text-slate-500">
                        Em andamento
                    </p>

                    <p class="mt-3 text-3xl font-bold text-slate-800">
                        {{ $processosEmAndamento }}
                    </p>

                    <p class="mt-1 text-xs text-slate-400">
                        Processos em tramitação
                    </p>
                </div>

                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-100">
                    <svg class="h-6 w-6 text-amber-600"
                         fill="none"
                         stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>


        {{-- Finalizados --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-start justify-between">

                <div>
                    <p class="text-sm font-medium text-slate-500">
                        Concluídos
                    </p>

                    <p class="mt-3 text-3xl font-bold text-slate-800">
                        {{ $processosFinalizados }}
                    </p>

                    <p class="mt-1 text-xs text-slate-400">
                        Etapa atual finalizada
                    </p>
                </div>

                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-green-100">
                    <svg class="h-6 w-6 text-green-600"
                         fill="none"
                         stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>
        </div>


        {{-- Taxa --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-start justify-between">

                <div>
                    <p class="text-sm font-medium text-slate-500">
                        Taxa de conclusão
                    </p>

                    <p class="mt-3 text-3xl font-bold text-slate-800">
                        {{ number_format($taxaConclusao, 1, ',', '.') }}%
                    </p>

                    <p class="mt-1 text-xs text-slate-400">
                        No período selecionado
                    </p>
                </div>

                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-100">
                    <svg class="h-6 w-6 text-blue-600"
                         fill="none"
                         stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
            </div>
        </div>

    </div>


    {{-- ============================================================
         SEGUNDA LINHA DE INDICADORES
    ============================================================= --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">

        {{-- Votações --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-slate-500">
                Votações
            </p>

            <p class="mt-3 text-3xl font-bold text-slate-800">
                {{ $totalVotacoes }}
            </p>

            <div class="mt-3 flex gap-4 text-xs">
                <span class="text-amber-600">
                    {{ $votacoesAbertas }} abertas
                </span>

                <span class="text-slate-500">
                    {{ $votacoesEncerradas }} encerradas
                </span>
            </div>
        </div>


        {{-- Aprovadas --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-slate-500">
                Votações aprovadas
            </p>

            <p class="mt-3 text-3xl font-bold text-emerald-600">
                {{ $votacoesAprovadas }}
            </p>

            <p class="mt-1 text-xs text-slate-400">
                Resultado das rodadas
            </p>
        </div>


        {{-- Reprovadas --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-slate-500">
                Votações reprovadas
            </p>

            <p class="mt-3 text-3xl font-bold text-red-600">
                {{ $votacoesReprovadas }}
            </p>

            <p class="mt-1 text-xs text-slate-400">
                Resultado das rodadas
            </p>
        </div>


        {{-- Devolvidos --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-slate-500">
                Processos devolvidos
            </p>

            <p class="mt-3 text-3xl font-bold text-orange-600">
                {{ $processosDevolvidos }}
            </p>

            <p class="mt-1 text-xs text-slate-400">
                Situação atual
            </p>
        </div>

    </div>


    {{-- ============================================================
         GRÁFICO DE EVOLUÇÃO
    ============================================================= --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

        <div class="mb-6">
            <h2 class="text-lg font-bold text-slate-800">
                Evolução dos processos
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Entrada e movimentação dos processos ao longo do período.
            </p>
        </div>

        <div class="relative h-[340px]">
            <canvas id="evolucaoProcessos"></canvas>
        </div>

    </div>


    {{-- ============================================================
         GRÁFICOS
    ============================================================= --}}
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">

        {{-- Status --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

            <div class="mb-5">
                <h2 class="text-lg font-bold text-slate-800">
                    Status dos processos
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Distribuição da situação atual dos processos.
                </p>
            </div>

            <div class="relative h-[320px]">
                <canvas id="statusProcessos"></canvas>
            </div>

        </div>


        {{-- Tipos --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

            <div class="mb-5">
                <h2 class="text-lg font-bold text-slate-800">
                    Processos por etapa
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Distribuição conforme a etapa atual.
                </p>
            </div>

            <div class="relative h-[320px]">
                <canvas id="tiposProcessos"></canvas>
            </div>

        </div>

    </div>


    {{-- ============================================================
         VOTAÇÕES + RELATORES
    ============================================================= --}}
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">

        {{-- Resultado das votações --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

            <div class="mb-5">
                <h2 class="text-lg font-bold text-slate-800">
                    Resultado das votações
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Distribuição das rodadas encerradas.
                </p>
            </div>

            <div class="relative h-[300px]">
                <canvas id="resultadoVotacoes"></canvas>
            </div>

        </div>


        {{-- Ranking --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

            <div class="mb-5">
                <h2 class="text-lg font-bold text-slate-800">
                    Relatores
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Processos atribuídos no período selecionado.
                </p>
            </div>

            @if(isset($rankingRelatores) && $rankingRelatores->count())

                <div class="space-y-4">

                    @foreach($rankingRelatores as $index => $relator)

                        <div class="flex items-center gap-4">

                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full
                                {{ $index === 0
                                    ? 'bg-emerald-100 text-emerald-700'
                                    : 'bg-slate-100 text-slate-600' }}
                                text-sm font-bold">

                                {{ $index + 1 }}

                            </div>

                            <div class="min-w-0 flex-1">

                                <p class="truncate text-sm font-semibold text-slate-700">
                                    {{ $relator->nome ?? 'Relator não identificado' }}
                                </p>

                                <div class="mt-1 h-1.5 overflow-hidden rounded-full bg-slate-100">

                                    @php
                                        $maiorQuantidade = max(
                                            1,
                                            $rankingRelatores->max('processos_count')
                                        );

                                        $percentual = ($relator->processos_count / $maiorQuantidade) * 100;
                                    @endphp

                                    <div
                                        class="h-full rounded-full bg-emerald-500 transition-all"
                                        style="width: {{ $percentual }}%">
                                    </div>

                                </div>

                            </div>

                            <div class="text-right">

                                <p class="text-lg font-bold text-slate-800">
                                    {{ $relator->processos_count }}
                                </p>

                                <p class="text-[11px] text-slate-400">
                                    processos
                                </p>

                            </div>

                        </div>

                    @endforeach

                </div>

            @else

                <div class="flex h-[250px] items-center justify-center">

                    <div class="text-center">

                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100">
                            <svg class="h-6 w-6 text-slate-400"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M12 4.354a4 4 0 110 7.292M15 21H3v-1a6 6 0 0112 0v1zm6 0h-6v-1a6 6 0 0112 0v1zm-3-10a4 4 0 10-8 0"/>
                            </svg>
                        </div>

                        <p class="mt-3 text-sm font-medium text-slate-600">
                            Nenhum relator encontrado
                        </p>

                        <p class="mt-1 text-xs text-slate-400">
                            Não há processos atribuídos no período.
                        </p>

                    </div>

                </div>

            @endif

        </div>

    </div>


    {{-- ============================================================
         RESUMO FINAL
    ============================================================= --}}
    <div class="overflow-hidden rounded-2xl bg-emerald-700 shadow-lg">

        <div class="flex flex-col gap-6 p-7 lg:flex-row lg:items-center lg:justify-between">

            <div class="max-w-2xl">

                <div class="flex items-center gap-3">

                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/10">

                        <svg class="h-5 w-5 text-white"
                             fill="none"
                             stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h8l5 5v11a2 2 0 01-2 2z"/>
                        </svg>

                    </div>

                    <h2 class="text-lg font-bold text-white">
                        Visão geral do período
                    </h2>

                </div>

                <p class="mt-3 text-sm leading-6 text-emerald-50">

                    No período de
                    <strong>
                        {{ $inicio->format('d/m/Y') }}
                    </strong>
                    a
                    <strong>
                        {{ $fim->format('d/m/Y') }}
                    </strong>,
                    foram registrados
                    <strong>{{ $totalProcessos }}</strong>
                    processos, sendo
                    <strong>{{ $processosFinalizados }}</strong>
                    concluídos.

                </p>

            </div>


            {{-- Botão PDF --}}
            <button
                type="button"
                onclick="window.print()"
                class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-white px-5 py-3 text-sm font-bold text-emerald-700 shadow-sm transition hover:bg-emerald-50">

                <svg class="h-5 w-5"
                     fill="none"
                     stroke="currentColor"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M12 3v12m0 0l-4-4m4 4l4-4M5 21h14a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2z"/>
                </svg>

                Exportar relatório

            </button>

        </div>

    </div>

</div>


{{-- ================================================================
     CHART.JS
================================================================= --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | Dados vindos do Laravel
    |--------------------------------------------------------------------------
    */

    const evolucao = @json($evolucao);

    const processosPorStatus = @json($processosPorStatus);

    const processosPorTipo = @json($processosPorTipo);

    const resultadoVotos = @json($resultadoVotos);


    /*
    |--------------------------------------------------------------------------
    | Configuração padrão
    |--------------------------------------------------------------------------
    */

    Chart.defaults.font.family = 'Inter, ui-sans-serif, system-ui, sans-serif';

    Chart.defaults.color = '#64748b';


    /*
    |--------------------------------------------------------------------------
    | Evolução
    |--------------------------------------------------------------------------
    */

    const evolucaoCanvas = document.getElementById('evolucaoProcessos');

    if (evolucaoCanvas) {

        new Chart(evolucaoCanvas, {
            type: 'line',

            data: {
                labels: evolucao.labels,

                datasets: [
                    {
                        label: 'Processos recebidos',
                        data: evolucao.entraram,
                        borderColor: '#059669',
                        backgroundColor: 'rgba(5, 150, 105, 0.10)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.35,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    },
                    {
                        label: 'Finalizados',
                        data: evolucao.finalizados,
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, 0.05)',
                        borderWidth: 2,
                        fill: false,
                        tension: 0.35,
                        pointRadius: 3,
                        pointHoverRadius: 5
                    },
                    {
                        label: 'Devolvidos',
                        data: evolucao.devolvidos,
                        borderColor: '#f97316',
                        backgroundColor: 'rgba(249, 115, 22, 0.05)',
                        borderWidth: 2,
                        fill: false,
                        tension: 0.35,
                        pointRadius: 3,
                        pointHoverRadius: 5
                    }
                ]
            },

            options: {
                responsive: true,
                maintainAspectRatio: false,

                interaction: {
                    intersect: false,
                    mode: 'index'
                },

                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20
                        }
                    },

                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 12,
                        cornerRadius: 10
                    }
                },

                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },

                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    }


    /*
    |--------------------------------------------------------------------------
    | Status
    |--------------------------------------------------------------------------
    */

    const statusCanvas = document.getElementById('statusProcessos');

    if (statusCanvas) {

        new Chart(statusCanvas, {
            type: 'doughnut',

            data: {
                labels: Object.keys(processosPorStatus),

                datasets: [{
                    data: Object.values(processosPorStatus),

                    backgroundColor: [
                        '#f59e0b',
                        '#10b981',
                        '#8b5cf6',
                        '#f97316',
                        '#64748b',
                        '#2563eb'
                    ],

                    borderWidth: 3,
                    borderColor: '#ffffff'
                }]
            },

            options: {
                responsive: true,
                maintainAspectRatio: false,

                cutout: '68%',

                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 18
                        }
                    },

                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 12,
                        cornerRadius: 10
                    }
                }
            }
        });
    }


    /*
    |--------------------------------------------------------------------------
    | Tipos / Etapas
    |--------------------------------------------------------------------------
    */

    const tiposCanvas = document.getElementById('tiposProcessos');

    if (tiposCanvas) {

        new Chart(tiposCanvas, {
            type: 'bar',

            data: {
                labels: Object.keys(processosPorTipo),

                datasets: [{
                    label: 'Processos',
                    data: Object.values(processosPorTipo),
                    backgroundColor: '#059669',
                    borderRadius: 8,
                    borderSkipped: false
                }]
            },

            options: {
                responsive: true,
                maintainAspectRatio: false,

                plugins: {
                    legend: {
                        display: false
                    },

                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 12,
                        cornerRadius: 10
                    }
                },

                scales: {
                    x: {
                        grid: {
                            display: false
                        },

                        ticks: {
                            maxRotation: 0
                        }
                    },

                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    }


    /*
    |--------------------------------------------------------------------------
    | Resultado das votações
    |--------------------------------------------------------------------------
    */

    const votacoesCanvas = document.getElementById('resultadoVotacoes');

    if (votacoesCanvas) {

        new Chart(votacoesCanvas, {
            type: 'bar',

            data: {
                labels: Object.keys(resultadoVotos),

                datasets: [{
                    label: 'Votações',
                    data: Object.values(resultadoVotos),
                    backgroundColor: [
                        '#059669',
                        '#ef4444'
                    ],
                    borderRadius: 8,
                    borderSkipped: false
                }]
            },

            options: {
                responsive: true,
                maintainAspectRatio: false,

                plugins: {
                    legend: {
                        display: false
                    },

                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 12,
                        cornerRadius: 10
                    }
                },

                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },

                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    }

});
</script>


{{-- ================================================================
     IMPRESSÃO
================================================================= --}}
<style>
@media print {

    body {
        background: white !important;
    }

    aside,
    header,
    nav {
        display: none !important;
    }

    main {
        width: 100% !important;
        background: white !important;
    }

    main > div {
        padding: 0 !important;
    }

    button {
        display: none !important;
    }

    canvas {
        max-height: 300px !important;
    }

    .shadow-sm,
    .shadow-lg {
        box-shadow: none !important;
    }

    @page {
        size: A4;
        margin: 12mm;
    }
}
</style>

@endsection