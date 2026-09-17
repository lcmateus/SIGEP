@extends('layouts.main_layout', [
    'titulo' => 'Processos Publicos',
])

@section('content')
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-sm font-bold text-emerald-700 uppercase">Processos Publicos</h3>
        </div>

        <table class="w-full border-collapse">
            <thead class="bg-slate-100">
                <tr class="text-left text-sm text-emerald-700">
                    <th class="p-4">Número SEI</th>
                    <th class="p-4">Relator</th>
                    <th class="p-4">Etapa Atual</th>
                    <th class="p-4">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 text-sm">
                @forelse($processos as $processo)
                    <tr>
                        <td class="p-4 font-medium text-emerald-900">{{ $processo->numero_sei }}</td>
                        <td class="p-4">{{ $processo->relator?->nome ?? '-' }}</td>
                        <td class="p-4">{{ $processo->etapa_atual?->tipo_display ?? 'Sem etapa' }}</td>
                        <td class="p-4">
                            <button type="button" data-abrir-detalhe="#detalhe-pub-{{ $loop->index }}"
                                class="text-white bg-blue-600 px-4 py-1 rounded-lg font-bold hover:bg-blue-700 transition-colors">
                                Ver Detalhes
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="p-4 text-slate-500" colspan="4">Nenhum processo publico.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @foreach($processos as $processo)
        <div id="detalhe-pub-{{ $loop->index }}" class="detalhe-overlay hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-xl max-w-3xl w-full max-h-[85vh] overflow-y-auto p-6">
                <div class="flex items-start justify-between gap-4 mb-5">
                    <div>
                        <h2 class="font-bold text-emerald-900 text-2xl">Processo SEI {{ $processo->numero_sei }}</h2>
                        <p class="text-sm text-slate-600 mt-1">
                            <span class="font-bold text-emerald-900">Relator:</span>
                            {{ $processo->relator?->nome ?? 'Nao designado' }}
                            ·
                            <span class="font-bold text-emerald-900">Etapa atual:</span>
                            {{ $processo->etapa_atual ? ($processo->etapa_atual->tipo_display . ' (' . $processo->etapa_atual->status_display . ')') : 'Sem etapa' }}
                        </p>
                    </div>
                    <button type="button" data-fechar-detalhe
                        class="text-slate-400 hover:text-slate-700 text-xl font-bold leading-none">X</button>
                </div>

                @include('processos.partials.detalhe-abas', [
                    'uid' => 'pub-' . $loop->index,
                    'processo' => $processo,
                    'documentosPorEtapa' => $documentosPorEtapa,
                    'rodadas' => $rodadasPorProcesso->get($processo->numero_sei, collect()),
                    'somenteLeitura' => true,
                ])
            </div>
        </div>
    @endforeach
@endsection
