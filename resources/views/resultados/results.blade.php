@extends('layouts.main_layout')

@section('titulo', 'Resultados e Relatórios | SIGEP')

@section('content')
@php
    $formatarNumero = fn ($valor) => number_format((int) $valor, 0, ',', '.');

    $cards = [
        [
            'titulo' => 'Processos recebidos',
            'valor' => $totalProcessos ?? 0,
            'descricao' => 'No período selecionado',
            'cor' => 'emerald',
            'icone' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h6l5 5v11a2 2 0 01-2 2z',
        ],
        [
            'titulo' => 'Em andamento',
            'valor' => $processosEmAndamento ?? 0,
            'descricao' => 'Processos em tramitação',
            'cor' => 'amber',
            'icone' => 'M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z',
        ],
        [
            'titulo' => 'Concluídos',
            'valor' => $processosFinalizados ?? 0,
            'descricao' => 'Etapa atual finalizada',
            'cor' => 'blue',
            'icone' => 'M5 13l4 4L19 7',
        ],
        [
            'titulo' => 'Taxa de conclusão',
            'valor' => number_format((float) ($taxaConclusao ?? 0), 1, ',', '.') . '%',
            'descricao' => 'No período selecionado',
            'cor' => 'violet',
            'icone' => 'M13 10V3L4 14h7v7l9-11h-7z',
        ],
    ];

    $evolucao = $evolucao ?? [
        'labels' => [],
        'entraram' => [],
        'finalizados' => [],
        'devolvidos' => [],
    ];

    $processosPorStatus = $processosPorStatus ?? collect();
    $processosPorTipo = $processosPorTipo ?? collect();
    $resultadoVotos = $resultadoVotos ?? collect();
    $rankingRelatores = $rankingRelatores ?? collect();
    $anosDisponiveis = $anosDisponiveis ?? collect();
@endphp

<div class="mx-auto w-full max-w-7xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">

    {{-- Cabeçalho e ações --}}
    <section class="flex flex-col gap-5 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6 lg:flex-row lg:items-center lg:justify-between">
        <div class="flex items-start gap-4">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 3v18h18M7 14l4-4 4 4 6-7"/>
                </svg>
            </div>

            <div>
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-emerald-700">
                    SIGEP · Indicadores
                </p>
                <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                    Resultados
                </h1>
                <p class="mt-1 max-w-2xl text-sm text-slate-500">
                    Acompanhe processos, votações e atividade do sistema no período selecionado.
                </p>
            </div>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
            <form method="GET"
                  action="{{ route('resultados') }}"
                  class="flex flex-col gap-1.5">
                <label for="periodo" class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                    Período do relatório
                </label>

                <select id="periodo"
                        name="periodo"
                        onchange="this.form.submit()"
                        class="min-w-52 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 shadow-sm outline-none transition focus:border-emerald-600 focus:ring-4 focus:ring-emerald-100">
                    <option value="hoje" @selected(($periodo ?? '30') === 'hoje')>
                        Hoje
                    </option>
                    <option value="15" @selected(($periodo ?? '30') === '15')>
                        Últimos 15 dias
                    </option>
                    <option value="30" @selected(($periodo ?? '30') === '30')>
                        Últimos 30 dias
                    </option>
                    <option value="3" @selected(($periodo ?? '30') === '3')>
                        Últimos 3 meses
                    </option>
                    <option value="6" @selected(($periodo ?? '30') === '6')>
                        Últimos 6 meses
                    </option>
                    <option value="12" @selected(($periodo ?? '30') === '12')>
                        Último ano
                    </option>

                    @if($anosDisponiveis->isNotEmpty())
                        <optgroup label="Anos">
                            @foreach($anosDisponiveis as $ano)
                                <option value="{{ $ano }}"
                                        @selected((string) ($periodo ?? '') === (string) $ano)>
                                    {{ $ano }}
                                </option>
                            @endforeach
                        </optgroup>
                    @endif
                </select>
            </form>

            @if(\Illuminate\Support\Facades\Route::has('resultados.pdf'))
                <a href="{{ route('resultados.pdf', ['periodo' => $periodo ?? '30']) }}"
                   class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-emerald-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-800 focus:outline-none focus:ring-4 focus:ring-emerald-200">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 16V4m0 12l-4-4m4 4l4-4M5 20h14"/>
                    </svg>
                    Baixar PDF
                </a>
            @endif
        </div>
    </section>

    {{-- Período selecionado --}}
    <section class="flex flex-col gap-2 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-white text-emerald-700 shadow-sm">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </span>
            <div>
                <p class="text-xs font-bold uppercase tracking-wide text-emerald-700">
                    Período analisado
                </p>
                <p class="text-sm font-semibold text-emerald-950">
                    {{ $periodoLabel ?? 'Últimos 30 dias' }}
                </p>
            </div>
        </div>

        <p class="text-sm text-emerald-800">
            {{ $inicio->format('d/m/Y') }} a {{ $fim->format('d/m/Y') }}
        </p>
    </section>

    {{-- Indicadores principais --}}
    <section aria-label="Indicadores principais"
             class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach($cards as $card)
            @php
                $cores = [
                    'emerald' => ['fundo' => 'bg-emerald-100', 'texto' => 'text-emerald-700'],
                    'amber' => ['fundo' => 'bg-amber-100', 'texto' => 'text-amber-700'],
                    'blue' => ['fundo' => 'bg-blue-100', 'texto' => 'text-blue-700'],
                    'violet' => ['fundo' => 'bg-violet-100', 'texto' => 'text-violet-700'],
                ];
                $cor = $cores[$card['cor']];
            @endphp

            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-medium text-slate-500">
                            {{ $card['titulo'] }}
                        </p>
                        <p class="mt-3 text-3xl font-bold tracking-tight text-slate-900">
                            @if(is_numeric($card['valor']))
                                {{ $formatarNumero($card['valor']) }}
                            @else
                                {{ $card['valor'] }}
                            @endif
                        </p>
                        <p class="mt-1 text-xs text-slate-400">
                            {{ $card['descricao'] }}
                        </p>
                    </div>

                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl {{ $cor['fundo'] }} {{ $cor['texto'] }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="{{ $card['icone'] }}"/>
                        </svg>
                    </span>
                </div>
            </article>
        @endforeach
    </section>

    {{-- Evolução dos processos --}}
    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="mb-5 flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-900">
                    Evolução dos processos
                </h2>
                <p class="text-sm text-slate-500">
                    Entradas, finalizações e devoluções ao longo do período.
                </p>
            </div>
            <span class="text-xs font-medium text-slate-400">
                Dados agrupados por mês
            </span>
        </div>

        <div class="relative h-72 sm:h-80">
            <canvas id="evolucaoProcessos" aria-label="Gráfico de evolução dos processos"></canvas>
        </div>
    </section>

    {{-- Distribuições --}}
    <section class="grid grid-cols-1 gap-4 xl:grid-cols-3">
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="mb-4">
                <h2 class="text-base font-bold text-slate-900">Processos por situação</h2>
                <p class="mt-1 text-sm text-slate-500">Situação atual dos processos recebidos.</p>
            </div>
            <div class="relative h-64">
                <canvas id="statusProcessos" aria-label="Gráfico de processos por situação"></canvas>
            </div>
            @if($processosPorStatus->isEmpty())
                <p class="mt-3 text-center text-sm text-slate-400">
                    Não há processos neste período.
                </p>
            @endif
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="mb-4">
                <h2 class="text-base font-bold text-slate-900">Tipos de etapa</h2>
                <p class="mt-1 text-sm text-slate-500">Tipo da etapa atual de cada processo.</p>
            </div>
            <div class="relative h-64">
                <canvas id="tiposProcessos" aria-label="Gráfico de processos por tipo de etapa"></canvas>
            </div>
            @if($processosPorTipo->isEmpty())
                <p class="mt-3 text-center text-sm text-slate-400">
                    Não há etapas neste período.
                </p>
            @endif
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="mb-4">
                <h2 class="text-base font-bold text-slate-900">Resultado das votações</h2>
                <p class="mt-1 text-sm text-slate-500">Votações concluídas no período.</p>
            </div>
            <div class="relative h-64">
                <canvas id="resultadoVotacoes" aria-label="Gráfico do resultado das votações"></canvas>
            </div>
            @if($totalVotacoes === 0)
                <p class="mt-3 text-center text-sm text-slate-400">
                    Não há votações neste período.
                </p>
            @endif
        </article>
    </section>

    {{-- Resumo operacional --}}
    <section class="grid grid-cols-1 gap-4 lg:grid-cols-2">
        <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                <h2 class="text-base font-bold text-slate-900">Resumo das votações</h2>
                <p class="mt-1 text-sm text-slate-500">Movimentação registrada no período.</p>
            </div>

            <div class="grid grid-cols-2 gap-px bg-slate-100 sm:grid-cols-4">
                <div class="bg-white p-5">
                    <p class="text-xs font-medium text-slate-500">Total</p>
                    <p class="mt-2 text-2xl font-bold text-slate-900">{{ $formatarNumero($totalVotacoes) }}</p>
                </div>
                <div class="bg-white p-5">
                    <p class="text-xs font-medium text-slate-500">Abertas</p>
                    <p class="mt-2 text-2xl font-bold text-amber-600">{{ $formatarNumero($votacoesAbertas) }}</p>
                </div>
                <div class="bg-white p-5">
                    <p class="text-xs font-medium text-slate-500">Encerradas</p>
                    <p class="mt-2 text-2xl font-bold text-blue-700">{{ $formatarNumero($votacoesEncerradas) }}</p>
                </div>
                <div class="bg-white p-5">
                    <p class="text-xs font-medium text-slate-500">Votos registrados</p>
                    <p class="mt-2 text-2xl font-bold text-emerald-700">{{ $formatarNumero($totalVotos) }}</p>
                </div>
            </div>

            <div class="px-5 py-4 sm:px-6">
                <div class="flex flex-wrap gap-x-6 gap-y-2 text-sm">
                    <span class="inline-flex items-center gap-2 text-slate-600">
                        <span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                        Aprovadas: <strong class="text-slate-900">{{ $formatarNumero($votacoesAprovadas) }}</strong>
                    </span>
                    <span class="inline-flex items-center gap-2 text-slate-600">
                        <span class="h-2.5 w-2.5 rounded-full bg-rose-500"></span>
                        Reprovadas: <strong class="text-slate-900">{{ $formatarNumero($votacoesReprovadas) }}</strong>
                    </span>
                </div>
            </div>
        </article>

        <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                <h2 class="text-base font-bold text-slate-900">Participação dos membros</h2>
                <p class="mt-1 text-sm text-slate-500">Resumo da composição de usuários do sistema.</p>
            </div>

            <div class="grid grid-cols-2 gap-px bg-slate-100 sm:grid-cols-4">
                <div class="bg-white p-5">
                    <p class="text-xs font-medium text-slate-500">Membros</p>
                    <p class="mt-2 text-2xl font-bold text-slate-900">{{ $formatarNumero($totalMembros) }}</p>
                </div>
                <div class="bg-white p-5">
                    <p class="text-xs font-medium text-slate-500">Ativos</p>
                    <p class="mt-2 text-2xl font-bold text-emerald-700">{{ $formatarNumero($membrosAtivos) }}</p>
                </div>
                <div class="bg-white p-5">
                    <p class="text-xs font-medium text-slate-500">Novos no período</p>
                    <p class="mt-2 text-2xl font-bold text-blue-700">{{ $formatarNumero($novosMembros) }}</p>
                </div>
                <div class="bg-white p-5">
                    <p class="text-xs font-medium text-slate-500">Administradores</p>
                    <p class="mt-2 text-2xl font-bold text-violet-700">{{ $formatarNumero($totalAdministradores) }}</p>
                </div>
            </div>

            <div class="px-5 py-4 sm:px-6">
                <div class="mb-2 flex items-center justify-between text-sm">
                    <span class="font-medium text-slate-600">Membros ativos</span>
                    <span class="font-semibold text-slate-900">
                        {{ $totalMembros > 0 ? number_format(($membrosAtivos / $totalMembros) * 100, 1, ',', '.') : '0,0' }}%
                    </span>
                </div>
                <div class="h-2.5 overflow-hidden rounded-full bg-slate-100">
                    <div class="h-full rounded-full bg-emerald-600 transition-all"
                         style="width: {{ $totalMembros > 0 ? min(100, ($membrosAtivos / $totalMembros) * 100) : 0 }}%">
                    </div>
                </div>
            </div>
        </article>
    </section>

    {{-- Resumo dos votos e ranking --}}
    <section class="grid grid-cols-1 gap-4 lg:grid-cols-2">
        <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                <h2 class="text-base font-bold text-slate-900">Distribuição dos votos</h2>
                <p class="mt-1 text-sm text-slate-500">Opções registradas nas votações do período.</p>
            </div>

            <div class="divide-y divide-slate-100">
                @php
                    $linhasVotos = [
                        ['Aprova', $votosAprova, 'bg-emerald-500'],
                        ['Desaprova', $votosDesaprova, 'bg-rose-500'],
                        ['Aprova com ressalva', $votosRessalva, 'bg-amber-500'],
                        ['Abstenções', $votosAbstencao, 'bg-slate-400'],
                    ];
                @endphp

                @foreach($linhasVotos as [$rotulo, $quantidade, $cor])
                    @php
                        $percentualVoto = $totalVotos > 0 ? ($quantidade / $totalVotos) * 100 : 0;
                    @endphp
                    <div class="px-5 py-4 sm:px-6">
                        <div class="mb-2 flex items-center justify-between gap-4">
                            <span class="inline-flex items-center gap-2 text-sm text-slate-600">
                                <span class="h-2.5 w-2.5 rounded-full {{ $cor }}"></span>
                                {{ $rotulo }}
                            </span>
                            <span class="text-sm font-semibold text-slate-900">
                                {{ $formatarNumero($quantidade) }}
                                <span class="font-normal text-slate-400">
                                    ({{ number_format($percentualVoto, 1, ',', '.') }}%)
                                </span>
                            </span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-slate-100">
                            <div class="h-full rounded-full {{ $cor }}"
                                 style="width: {{ min(100, $percentualVoto) }}%">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </article>

        <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                <h2 class="text-base font-bold text-slate-900">Relatores em destaque</h2>
                <p class="mt-1 text-sm text-slate-500">Processos recebidos no período selecionado.</p>
            </div>

            @if($rankingRelatores->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-slate-50">
                            <tr>
                                <th scope="col" class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Relator
                                </th>
                                <th scope="col" class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Processos
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($rankingRelatores as $index => $relator)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center gap-3">
                                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-100 text-xs font-bold text-emerald-800">
                                                {{ $index + 1 }}
                                            </span>
                                            <span class="text-sm font-medium text-slate-800">
                                                {{ $relator->nome }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5 text-right text-sm font-semibold text-slate-900">
                                        {{ $formatarNumero($relator->processos_count) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-5 py-10 text-center">
                    <p class="text-sm font-medium text-slate-600">Sem dados de relatores neste período</p>
                    <p class="mt-1 text-xs text-slate-400">Os resultados aparecerão quando houver processos atribuídos.</p>
                </div>
            @endif
        </article>
    </section>

    <p class="px-1 text-xs text-slate-400">
        Os indicadores são calculados a partir dos registros do SIGEP e respeitam o período selecionado.
    </p>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof Chart === 'undefined') {
        return;
    }

    Chart.defaults.font.family = 'Inter, ui-sans-serif, system-ui, sans-serif';
    Chart.defaults.color = '#64748b';

    const evolucao = @json($evolucao);
    const processosPorStatus = @json($processosPorStatus);
    const processosPorTipo = @json($processosPorTipo);
    const resultadoVotos = @json($resultadoVotos);

    const coresStatus = [
        '#059669',
        '#f59e0b',
        '#8b5cf6',
        '#f97316',
        '#64748b',
        '#2563eb'
    ];

    const opcoesEixos = {
        x: {
            grid: { display: false },
            ticks: { maxRotation: 0 }
        },
        y: {
            beginAtZero: true,
            ticks: { precision: 0 },
            grid: { color: 'rgba(148, 163, 184, 0.16)' }
        }
    };

    const canvasEvolucao = document.getElementById('evolucaoProcessos');

    if (canvasEvolucao) {
        new Chart(canvasEvolucao, {
            type: 'line',
            data: {
                labels: evolucao.labels || [],
                datasets: [
                    {
                        label: 'Processos recebidos',
                        data: evolucao.entraram || [],
                        borderColor: '#059669',
                        backgroundColor: 'rgba(5, 150, 105, 0.10)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.35,
                        pointRadius: 3,
                        pointHoverRadius: 5
                    },
                    {
                        label: 'Finalizados',
                        data: evolucao.finalizados || [],
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, 0.06)',
                        borderWidth: 2,
                        fill: false,
                        tension: 0.35,
                        pointRadius: 3,
                        pointHoverRadius: 5
                    },
                    {
                        label: 'Devolvidos',
                        data: evolucao.devolvidos || [],
                        borderColor: '#f97316',
                        backgroundColor: 'rgba(249, 115, 22, 0.06)',
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
                interaction: { intersect: false, mode: 'index' },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { usePointStyle: true, padding: 18 }
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 12,
                        cornerRadius: 10
                    }
                },
                scales: opcoesEixos
            }
        });
    }

    const canvasStatus = document.getElementById('statusProcessos');

    if (canvasStatus) {
        new Chart(canvasStatus, {
            type: 'doughnut',
            data: {
                labels: Object.keys(processosPorStatus || {}),
                datasets: [{
                    data: Object.values(processosPorStatus || {}),
                    backgroundColor: coresStatus,
                    borderColor: '#ffffff',
                    borderWidth: 3,
                    hoverOffset: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { usePointStyle: true, padding: 14 }
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

    const canvasTipos = document.getElementById('tiposProcessos');

    if (canvasTipos) {
        new Chart(canvasTipos, {
            type: 'bar',
            data: {
                labels: Object.keys(processosPorTipo || {}),
                datasets: [{
                    label: 'Processos',
                    data: Object.values(processosPorTipo || {}),
                    backgroundColor: '#059669',
                    borderRadius: 7,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 12,
                        cornerRadius: 10
                    }
                },
                scales: opcoesEixos
            }
        });
    }

    const canvasVotacoes = document.getElementById('resultadoVotacoes');

    if (canvasVotacoes) {
        new Chart(canvasVotacoes, {
            type: 'bar',
            data: {
                labels: Object.keys(resultadoVotos || {}),
                datasets: [{
                    label: 'Votações',
                    data: Object.values(resultadoVotos || {}),
                    backgroundColor: ['#059669', '#f43f5e'],
                    borderRadius: 7,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 12,
                        cornerRadius: 10
                    }
                },
                scales: opcoesEixos
            }
        });
    }
});
</script>
@endpush