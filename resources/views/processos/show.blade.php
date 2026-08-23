@extends('layouts.main_layout', [
    'titulo' => 'Processo',
])

@section('content')
    <div class="max-w-4xl">
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6 mb-6">
            <div class="flex items-start justify-between gap-6">
                <div>
                    <h2 class="font-bold text-emerald-900 text-2xl">Processo SEI {{ $processo->numero_sei }}</h2>
                    <p class="text-sm text-slate-600 mt-2">
                        <span class="font-bold text-emerald-900">Relator:</span>
                        {{ $processo->relator?->nome ?? 'Nao designado' }}
                        ·
                        <span class="font-bold text-emerald-900">Administrador responsavel:</span>
                        {{ $processo->administrador?->nome ?? 'Nao informado' }}
                    </p>
                    <p class="text-sm text-slate-500 mt-1">
                        Admissao: {{ $processo->data_admissao?->format('d/m/Y') ?? 'Nao informado' }}
                        @if($processo->data_devolucao)
                            · Devolucao: {{ $processo->data_devolucao->format('d/m/Y') }}
                        @endif
                    </p>
                </div>

                @if($processo->etapa_atual)
                    <span class="bg-emerald-600 px-5 py-1 rounded-md text-sm font-semibold tracking-wide text-white">
                        {{ $processo->etapa_atual->tipo_display }}
                    </span>
                @else
                    <span class="bg-slate-400 px-5 py-1 rounded-md text-sm font-semibold tracking-wide text-white">
                        Sem etapa
                    </span>
                @endif
            </div>

            <div class="mt-6 flex gap-3">
                <a href="{{ route('processos.index') }}" class="inline-block text-center bg-slate-200 hover:bg-slate-300 text-emerald-900 text-sm font-bold px-5 py-2 rounded-md">
                    Voltar
                </a>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6">
            @include('processos.partials.detalhe-abas', [
                'uid' => 'show',
                'processo' => $processo,
                'documentosPorEtapa' => $documentosPorEtapa,
                'rodadas' => $rodadas,
            ])
        </div>
    </div>
@endsection
