@extends('layouts/main_layout', [
    'title' => 'Resultados - SIGEP',
    'titulo' => 'RESULTADOS',
    'usuario' => 'admin',
])

@section('content')
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <h3 class="text-sm font-bold text-emerald-700 uppercase mb-4">
            FILTRO
        </h3>

        <div class="grid grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    MÊS
                </label>

                <select
                    class="w-full border border-slate-300 rounded-lg p-3 outline-none focus:ring-2 focus:ring-emerald-500">
                    <option>Maio</option>
                    <option>Junho</option>
                    <option>Julho</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    ANO
                </label>

                <select
                    class="w-full border border-slate-300 rounded-lg p-3 outline-none focus:ring-2 focus:ring-emerald-500">
                    <option>2025</option>
                    <option>2026</option>
                </select>
            </div>

            <div class="flex items-end">
                <button
                    class="w-full bg-emerald-600 text-white py-3 rounded-lg font-bold hover:bg-emerald-700 transition-all">
                    Filtrar
                </button>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 mt-6">
        <h3 class="text-sm font-bold text-emerald-700 uppercase mb-6">
            STATUS DOS PROCESSOS
        </h3>

        <div class="grid grid-cols-4 gap-4">

            <div class="border border-slate-200 rounded-xl p-5">
                <div class="flex items-center justify-between">
                    <div class="w-14 h-14 rounded-full bg-emerald-100 flex items-center justify-center text-2xl">
                        📄
                    </div>

                    <div class="text-right">
                        <h2 class="text-4xl font-bold text-emerald-700">
                            32
                        </h2>

                        <p class="text-sm font-semibold text-emerald-700">
                            Entraram
                        </p>
                    </div>
                </div>

                <p class="text-xs text-slate-500 mt-4">
                    Total no período
                </p>

                <div class="w-full h-2 bg-slate-200 rounded-full mt-4">
                    <div class="w-[21%] h-2 bg-emerald-600 rounded-full"></div>
                </div>

                <p class="text-right text-sm font-bold text-emerald-700 mt-2">
                    21%
                </p>
            </div>

            <div class="border border-slate-200 rounded-xl p-5">
                <div class="flex items-center justify-between">
                    <div class="w-14 h-14 rounded-full bg-orange-100 flex items-center justify-center text-2xl">
                        ⏳
                    </div>

                    <div class="text-right">
                        <h2 class="text-4xl font-bold text-orange-500">
                            18
                        </h2>

                        <p class="text-sm font-semibold text-orange-500">
                            Em andamento
                        </p>
                    </div>
                </div>

                <p class="text-xs text-slate-500 mt-4">
                    Total no período
                </p>

                <div class="w-full h-2 bg-slate-200 rounded-full mt-4">
                    <div class="w-[12%] h-2 bg-orange-500 rounded-full"></div>
                </div>

                <p class="text-right text-sm font-bold text-orange-500 mt-2">
                    12%
                </p>
            </div>

            <div class="border border-slate-200 rounded-xl p-5">
                <div class="flex items-center justify-between">
                    <div class="w-14 h-14 rounded-full bg-blue-100 flex items-center justify-center text-2xl">
                        ✔️
                    </div>

                    <div class="text-right">
                        <h2 class="text-4xl font-bold text-blue-600">
                            40
                        </h2>

                        <p class="text-sm font-semibold text-blue-600">
                            Concluídos
                        </p>
                    </div>
                </div>

                <p class="text-xs text-slate-500 mt-4">
                    Total no período
                </p>

                <div class="w-full h-2 bg-slate-200 rounded-full mt-4">
                    <div class="w-[26%] h-2 bg-blue-600 rounded-full"></div>
                </div>

                <p class="text-right text-sm font-bold text-blue-600 mt-2">
                    26%
                </p>
            </div>

            <div class="border border-slate-200 rounded-xl p-5">
                <div class="flex items-center justify-between">
                    <div class="w-14 h-14 rounded-full bg-purple-100 flex items-center justify-center text-2xl">
                        ➖
                    </div>

                    <div class="text-right">
                        <h2 class="text-4xl font-bold text-purple-600">
                            25
                        </h2>

                        <p class="text-sm font-semibold text-purple-600">
                            Sem início
                        </p>
                    </div>
                </div>

                <p class="text-xs text-slate-500 mt-4">
                    Total no período
                </p>

                <div class="w-full h-2 bg-slate-200 rounded-full mt-4">
                    <div class="w-[16%] h-2 bg-purple-600 rounded-full"></div>
                </div>

                <p class="text-right text-sm font-bold text-purple-600 mt-2">
                    16%
                </p>
            </div>

        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 mt-6">
        <h3 class="text-sm font-bold text-emerald-700 uppercase mb-6">
            RESULTADO DAS VOTAÇÕES CONCLUÍDAS
        </h3>

        <div class="grid grid-cols-2 gap-6">

            <div class="border border-slate-200 rounded-xl p-6">
                <div class="flex items-center justify-between">
                    <div class="w-16 h-16 rounded-full bg-emerald-100 flex items-center justify-center text-3xl">
                        👍
                    </div>

                    <div class="text-right">
                        <h2 class="text-5xl font-bold text-emerald-700">
                            65
                        </h2>

                        <p class="text-sm font-semibold text-emerald-700">
                            Aprovados
                        </p>
                    </div>
                </div>

                <p class="text-xs text-slate-500 mt-4">
                    Total no período
                </p>

                <div class="w-full h-2 bg-slate-200 rounded-full mt-4">
                    <div class="w-[68%] h-2 bg-emerald-600 rounded-full"></div>
                </div>

                <p class="text-right text-sm font-bold text-emerald-700 mt-2">
                    68%
                </p>
            </div>

            <div class="border border-slate-200 rounded-xl p-6">
                <div class="flex items-center justify-between">
                    <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center text-3xl">
                        👎
                    </div>

                    <div class="text-right">
                        <h2 class="text-5xl font-bold text-red-500">
                            30
                        </h2>

                        <p class="text-sm font-semibold text-red-500">
                            Recusados
                        </p>
                    </div>
                </div>

                <p class="text-xs text-slate-500 mt-4">
                    Total no período
                </p>

                <div class="w-full h-2 bg-slate-200 rounded-full mt-4">
                    <div class="w-[32%] h-2 bg-red-500 rounded-full"></div>
                </div>

                <p class="text-right text-sm font-bold text-red-500 mt-2">
                    32%
                </p>
            </div>

        </div>
    </div>

    <div class="flex justify-center mt-8 mb-10">
        <button
            class="border border-emerald-600 text-emerald-700 font-bold px-10 py-4 rounded-xl hover:bg-emerald-50 transition-all">
            📄 Exportar relatório em PDF
        </button>
    </div>
@endsection
