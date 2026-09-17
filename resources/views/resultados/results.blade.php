@extends('layouts/main_layout', [
    'title' => 'Resultados - SIGEP',
    'titulo' => 'RESULTADOS',
    'usuario' => 'admin',
])

@section('content')

<div class="space-y-6">

    {{-- CABEÇALHO --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <p class="text-sm font-semibold text-emerald-600 uppercase tracking-wide">
                Painel de indicadores
            </p>

            <h2 class="text-2xl font-bold text-slate-800 mt-1">
                Resultados e estatísticas
            </h2>

            <p class="text-sm text-slate-500 mt-1">
                Acompanhe os principais indicadores dos processos e votações do SIGEP.
            </p>
        </div>

        <div class="bg-emerald-50 border border-emerald-100 rounded-xl px-4 py-3">
            <p class="text-xs font-semibold text-emerald-600 uppercase">
                Período selecionado
            </p>

            <p class="text-sm font-bold text-emerald-800 mt-1">
                {{ $nomePeriodo }}
            </p>
        </div>
    </div>


    {{-- FILTROS --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">

        <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center">
                <span class="text-lg">⚙️</span>
            </div>

            <div>
                <h3 class="text-sm font-bold text-slate-800 uppercase">
                    Filtros
                </h3>

                <p class="text-xs text-slate-500">
                    Selecione o período que deseja analisar.
                </p>
            </div>
        </div>

        <form method="GET" action="{{ route('relatorios.index') }}">

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-2">
                        Período
                    </label>

                    <select
                        name="periodo"
                        class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">

                        <option value="hoje" @selected($periodo === 'hoje')>
                            Hoje
                        </option>

                        <option value="15" @selected($periodo === '15')>
                            Últimos 15 dias
                        </option>

                        <option value="30" @selected($periodo === '30')>
                            Últimos 30 dias
                        </option>

                        <option value="3" @selected($periodo === '3')>
                            Últimos 3 meses
                        </option>

                        <option value="6" @selected($periodo === '6')>
                            Últimos 6 meses
                        </option>

                        <option value="12" @selected($periodo === '12')>
                            Último ano
                        </option>

                        @foreach($anosDisponiveis as $ano)
                            <option
                                value="{{ $ano }}"
                                @selected($periodo == $ano)>
                                Ano de {{ $ano }}
                            </option>
                        @endforeach

                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-2">
                        Início
                    </label>

                    <div class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700">
                        {{ $inicio->format('d/m/Y') }}
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-2">
                        Fim
                    </label>

                    <div class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700">
                        {{ $fim->format('d/m/Y') }}
                    </div>
                </div>

                <div class="flex items-end">

                    <button
                        type="submit"
                        class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 rounded-xl transition-all shadow-sm">

                        Atualizar resultados
                    </button>

                </div>

            </div>

        </form>

    </div>


    {{-- INDICADORES --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

        {{-- PROCESSOS --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">

            <div class="flex items-center justify-between">

                <div>
                    <p class="text-xs font-bold text-slate-500 uppercase">
                        Processos recebidos
                    </p>

                    <p class="text-3xl font-bold text-slate-800 mt-2">
                        {{ $totalProcessos }}
                    </p>
                </div>

                <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center">
                    <span class="text-xl">📄</span>
                </div>

            </div>

            <p class="text-xs text-slate-500 mt-4">
                Processos admitidos no período.
            </p>

        </div>


        {{-- ANDAMENTO --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">

            <div class="flex items-center justify-between">

                <div>
                    <p class="text-xs font-bold text-slate-500 uppercase">
                        Em andamento
                    </p>

                    <p class="text-3xl font-bold text-orange-500 mt-2">
                        {{ $processosEmAndamento }}
                    </p>
                </div>

                <div class="w-12 h-12 rounded-xl bg-orange-100 flex items-center justify-center">
                    <span class="text-xl">⏳</span>
                </div>

            </div>

            <p class="text-xs text-slate-500 mt-4">
                Processos atualmente em andamento.
            </p>

        </div>


        {{-- FINALIZADOS --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">

            <div class="flex items-center justify-between">

                <div>
                    <p class="text-xs font-bold text-slate-500 uppercase">
                        Finalizados
                    </p>

                    <p class="text-3xl font-bold text-blue-600 mt-2">
                        {{ $processosFinalizados }}
                    </p>
                </div>

                <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center">
                    <span class="text-xl">✓</span>
                </div>

            </div>

            <p class="text-xs text-slate-500 mt-4">
                Processos concluídos no período.
            </p>

        </div>


        {{-- TAXA --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">

            <div class="flex items-center justify-between">

                <div>
                    <p class="text-xs font-bold text-slate-500 uppercase">
                        Taxa de conclusão
                    </p>

                    <p class="text-3xl font-bold text-purple-600 mt-2">
                        {{ number_format($taxaConclusao, 1, ',', '.') }}%
                    </p>
                </div>

                <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center">
                    <span class="text-xl">📊</span>
                </div>

            </div>

            <div class="w-full h-2 bg-slate-100 rounded-full mt-4 overflow-hidden">

                <div
                    class="h-full bg-purple-500 rounded-full"
                    style="width: {{ min($taxaConclusao, 100) }}%">
                </div>

            </div>

        </div>

    </div>


    {{-- GRÁFICO DE EVOLUÇÃO --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-6">

            <div>
                <h3 class="text-sm font-bold text-slate-800 uppercase">
                    Evolução dos processos
                </h3>

                <p class="text-xs text-slate-500 mt-1">
                    Comparativo dos processos ao longo do período selecionado.
                </p>
            </div>

        </div>

        <div class="relative h-80">
            <canvas id="evolucaoProcessos"></canvas>
        </div>

    </div>


    {{-- STATUS + TIPOS --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

        {{-- STATUS --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">

            <div class="mb-5">
                <h3 class="text-sm font-bold text-slate-800 uppercase">
                    Status dos processos
                </h3>

                <p class="text-xs text-slate-500 mt-1">
                    Distribuição dos processos por situação.
                </p>
            </div>

            <div class="relative h-80">
                <canvas id="statusProcessos"></canvas>
            </div>

        </div>


        {{-- TIPOS --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">

            <div class="mb-5">
                <h3 class="text-sm font-bold text-slate-800 uppercase">
                    Tipos de processo
                </h3>

                <p class="text-xs text-slate-500 mt-1">
                    Distribuição conforme o tipo de etapa.
                </p>
            </div>

            <div class="relative h-80">
                <canvas id="tiposProcessos"></canvas>
            </div>

        </div>

    </div>


    {{-- VOTAÇÕES --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-6">

            <div>
                <h3 class="text-sm font-bold text-slate-800 uppercase">
                    Votações
                </h3>

                <p class="text-xs text-slate-500 mt-1">
                    Indicadores das votações realizadas no período.
                </p>
            </div>

            <div class="grid grid-cols-3 gap-3">

                <div class="bg-slate-50 rounded-xl px-4 py-3 text-center">
                    <p class="text-lg font-bold text-slate-800">
                        {{ $totalVotacoes }}
                    </p>

                    <p class="text-[10px] uppercase font-bold text-slate-500">
                        Total
                    </p>
                </div>

                <div class="bg-emerald-50 rounded-xl px-4 py-3 text-center">
                    <p class="text-lg font-bold text-emerald-700">
                        {{ $votacoesAprovadas }}
                    </p>

                    <p class="text-[10px] uppercase font-bold text-emerald-600">
                        Aprovadas
                    </p>
                </div>

                <div class="bg-red-50 rounded-xl px-4 py-3 text-center">
                    <p class="text-lg font-bold text-red-600">
                        {{ $votacoesReprovadas }}
                    </p>

                    <p class="text-[10px] uppercase font-bold text-red-500">
                        Reprovadas
                    </p>
                </div>

            </div>

        </div>

        <div class="relative h-80">
            <canvas id="resultadoVotacoes"></canvas>
        </div>

    </div>


    {{-- RESUMO DOS VOTOS --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">

        <div class="mb-6">
            <h3 class="text-sm font-bold text-slate-800 uppercase">
                Resultado dos votos
            </h3>

            <p class="text-xs text-slate-500 mt-1">
                Distribuição das opções registradas nas votações.
            </p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

            <div class="rounded-xl bg-emerald-50 border border-emerald-100 p-5">
                <p class="text-xs font-bold uppercase text-emerald-600">
                    Aprova
                </p>

                <p class="text-3xl font-bold text-emerald-700 mt-2">
                    {{ $resultadoVotos['aprova'] }}
                </p>
            </div>

            <div class="rounded-xl bg-red-50 border border-red-100 p-5">
                <p class="text-xs font-bold uppercase text-red-500">
                    Desaprova
                </p>

                <p class="text-3xl font-bold text-red-600 mt-2">
                    {{ $resultadoVotos['desaprova'] }}
                </p>
            </div>

            <div class="rounded-xl bg-blue-50 border border-blue-100 p-5">
                <p class="text-xs font-bold uppercase text-blue-600">
                    Aprova com ressalva
                </p>

                <p class="text-3xl font-bold text-blue-700 mt-2">
                    {{ $resultadoVotos['aprova com resalva'] }}
                </p>
            </div>

            <div class="rounded-xl bg-slate-50 border border-slate-200 p-5">
                <p class="text-xs font-bold uppercase text-slate-500">
                    Abstenção
                </p>

                <p class="text-3xl font-bold text-slate-700 mt-2">
                    {{ $resultadoVotos['abstenho'] }}
                </p>
            </div>

        </div>

    </div>


    {{-- RANKING --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">

        <div class="mb-6">
            <h3 class="text-sm font-bold text-slate-800 uppercase">
                Relatores com mais processos
            </h3>

            <p class="text-xs text-slate-500 mt-1">
                Ranking dos membros responsáveis por processos no período.
            </p>
        </div>

        <div class="space-y-4">

            @forelse($rankingRelatores as $index => $relator)

                <div class="flex items-center gap-4">

                    <div class="w-9 h-9 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-sm">
                        {{ $index + 1 }}
                    </div>

                    <div class="flex-1">

                        <div class="flex items-center justify-between mb-2">

                            <p class="text-sm font-semibold text-slate-700">
                                {{ $relator->nome }}
                            </p>

                            <p class="text-xs font-bold text-slate-500">
                                {{ $relator->processos_count }} processos
                            </p>

                        </div>

                        @php
                            $maiorRanking = max(
                                $rankingRelatores->max('processos_count'),
                                1
                            );

                            $percentualRanking =
                                ($relator->processos_count / $maiorRanking) * 100;
                        @endphp

                        <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">

                            <div
                                class="h-full bg-emerald-500 rounded-full"
                                style="width: {{ $percentualRanking }}%">
                            </div>

                        </div>

                    </div>

                </div>

            @empty

                <div class="text-center py-10">

                    <p class="text-sm font-semibold text-slate-500">
                        Nenhum relator encontrado no período.
                    </p>

                </div>

            @endforelse

        </div>

    </div>


    {{-- RESUMO FINAL --}}
    <div class="bg-gradient-to-r from-emerald-700 to-emerald-600 rounded-2xl p-6 text-white">

        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">

            <div>

                <p class="text-emerald-100 text-xs font-bold uppercase tracking-wide">
                    Resumo do período
                </p>

                <h3 class="text-xl font-bold mt-2">
                    {{ $nomePeriodo }}
                </h3>

                <p class="text-sm text-emerald-100 mt-2">
                    {{ $totalProcessos }} processo(s) admitido(s),
                    {{ $processosFinalizados }} finalizado(s) e
                    {{ $totalVotacoes }} votação(ões) registrada(s).
                </p>

            </div>

            <div class="flex gap-3">

                <div class="bg-white/10 rounded-xl px-5 py-3 text-center">
                    <p class="text-2xl font-bold">
                        {{ $totalVotos }}
                    </p>

                    <p class="text-[10px] uppercase text-emerald-100 font-bold">
                        Votos
                    </p>
                </div>

                <div class="bg-white/10 rounded-xl px-5 py-3 text-center">
                    <p class="text-2xl font-bold">
                        {{ $membrosAtivos }}
                    </p>

                    <p class="text-[10px] uppercase text-emerald-100 font-bold">
                        Membros ativos
                    </p>
                </div>

            </div>

        </div>

    </div>


    {{-- EXPORTAÇÃO --}}
    <div class="flex justify-center pt-2 pb-8">

        <button
            type="button"
            class="border border-emerald-600 text-emerald-700 font-bold px-8 py-3 rounded-xl hover:bg-emerald-50 transition-all">

            📄 Exportar relatório em PDF

        </button>

    </div>

</div>


{{-- CHART.JS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | DADOS DO BACKEND
    |--------------------------------------------------------------------------
    */

    const evolucao = @json($evolucao);

    const processosPorStatus = @json($processosPorStatus);

    const processosPorTipo = @json($processosPorTipo);

    const resultadoVotos = @json($resultadoVotos);


    /*
    |--------------------------------------------------------------------------
    | EVOLUÇÃO DOS PROCESSOS
    |--------------------------------------------------------------------------
    */

    new Chart(document.getElementById('evolucaoProcessos'), {

        type: 'line',

        data: {

            labels: evolucao.map(item => item.label),

            datasets: [

                {
                    label: 'Recebidos',

                    data: evolucao.map(item => item.criados),

                    borderWidth: 3,

                    tension: 0.35,

                    fill: false
                },

                {
                    label: 'Finalizados',

                    data: evolucao.map(item => item.finalizados),

                    borderWidth: 3,

                    tension: 0.35,

                    fill: false
                },

                {
                    label: 'Devolvidos',

                    data: evolucao.map(item => item.devolvidos),

                    borderWidth: 2,

                    tension: 0.35,

                    fill: false
                },

                {
                    label: 'Arquivados',

                    data: evolucao.map(item => item.arquivados),

                    borderWidth: 2,

                    tension: 0.35,

                    fill: false
                }

            ]

        },

        options: {

            responsive: true,

            maintainAspectRatio: false,

            plugins: {

                legend: {
                    position: 'bottom'
                }

            },

            scales: {

                y: {
                    beginAtZero: true,

                    ticks: {
                        precision: 0
                    }
                }

            }

        }

    });


    /*
    |--------------------------------------------------------------------------
    | STATUS DOS PROCESSOS
    |--------------------------------------------------------------------------
    */

    new Chart(document.getElementById('statusProcessos'), {

        type: 'doughnut',

        data: {

            labels: Object.keys(processosPorStatus),

            datasets: [{

                data: Object.values(processosPorStatus),

                borderWidth: 2

            }]

        },

        options: {

            responsive: true,

            maintainAspectRatio: false,

            cutout: '65%',

            plugins: {

                legend: {
                    position: 'bottom'
                }

            }

        }

    });


    /*
    |--------------------------------------------------------------------------
    | TIPOS DE PROCESSO
    |--------------------------------------------------------------------------
    */

    new Chart(document.getElementById('tiposProcessos'), {

        type: 'bar',

        data: {

            labels: Object.keys(processosPorTipo),

            datasets: [{

                label: 'Processos',

                data: Object.values(processosPorTipo),

                borderRadius: 8

            }]

        },

        options: {

            responsive: true,

            maintainAspectRatio: false,

            plugins: {

                legend: {
                    display: false
                }

            },

            scales: {

                y: {

                    beginAtZero: true,

                    ticks: {
                        precision: 0
                    }

                }

            }

        }

    });


    /*
    |--------------------------------------------------------------------------
    | RESULTADO DOS VOTOS
    |--------------------------------------------------------------------------
    */

    new Chart(document.getElementById('resultadoVotacoes'), {

        type: 'bar',

        data: {

            labels: [
                'Aprova',
                'Desaprova',
                'Aprova com ressalva',
                'Abstenção'
            ],

            datasets: [{

                label: 'Votos',

                data: [

                    resultadoVotos['aprova'],

                    resultadoVotos['desaprova'],

                    resultadoVotos['aprova com resalva'],

                    resultadoVotos['abstenho']

                ],

                borderRadius: 8

            }]

        },

        options: {

            responsive: true,

            maintainAspectRatio: false,

            plugins: {

                legend: {
                    display: false
                }

            },

            scales: {

                y: {

                    beginAtZero: true,

                    ticks: {
                        precision: 0
                    }

                }

            }

        }

    });

});

</script>

@endsection