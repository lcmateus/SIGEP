@extends('layouts.main_layout', [
    'titulo' => 'Próxima Etapa',
])

@section('content')
    <div class="max-w-2xl">
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6 mb-6">
            <div>
                <h2 class="font-bold text-emerald-900 text-2xl">Processo SEI {{ $processo->numero_sei }}</h2>
                <p class="text-sm text-slate-600 mt-2">
                    <span class="font-bold text-emerald-900">Relator:</span>
                    {{ $processo->relator?->nome ?? 'Não designado' }}
                    ·
                    <span class="font-bold text-emerald-900">Secretário(a):</span>
                    {{ $processo->administrador?->nome ?? 'Não informado' }}
                </p>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-bold text-emerald-900 mb-2">Qual a próxima etapa?</h3>
            <p class="text-sm text-slate-600 mb-6">
                A votação deste processo foi concluída. Escolha como o processo deve prosseguir.
            </p>

            <div class="grid gap-4">
                <form method="POST" action="{{ route('processos.proxima-etapa.store', $processo) }}">
                    @csrf
                    <input type="hidden" name="tipo" value="{{ \App\Models\Etapa::TIPO_ACPP }}">
                    <button type="submit"
                        class="w-full text-left bg-yellow-50 hover:bg-yellow-100 border-2 border-yellow-400 rounded-xl p-5 transition-colors">
                        <span class="block text-lg font-bold text-emerald-900">Acordo de Conduta</span>
                        <span class="block text-sm text-slate-600 mt-1">Seguir para a etapa de Acordo de Conduta (ACPP).</span>
                    </button>
                </form>

                <form method="POST" action="{{ route('processos.proxima-etapa.store', $processo) }}">
                    @csrf
                    <input type="hidden" name="tipo" value="{{ \App\Models\Etapa::TIPO_PAE }}">
                    <button type="submit"
                        class="w-full text-left bg-red-50 hover:bg-red-100 border-2 border-red-400 rounded-xl p-5 transition-colors">
                        <span class="block text-lg font-bold text-emerald-900">Processo de Apuração</span>
                        <span class="block text-sm text-slate-600 mt-1">Seguir para a etapa de Processo de Apuração (PAE).</span>
                    </button>
                </form>

                <a href="{{ route('processos.show', $processo) }}"
                    class="inline-block text-center bg-slate-200 hover:bg-slate-300 text-emerald-900 text-sm font-bold px-5 py-2 rounded-md">
                    Voltar
                </a>
            </div>
        </div>
    </div>
@endsection
