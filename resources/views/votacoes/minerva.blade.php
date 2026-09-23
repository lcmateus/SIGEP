@extends('layouts.main_layout', [
    'titulo' => 'Votações Minerva',
])

@section('content')
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-sm font-bold text-emerald-700 uppercase">Desempates (Voto de Minerva)</h3>
        </div>

        <table class="w-full border-collapse">
            <thead class="bg-slate-100">
                <tr class="text-left text-sm text-emerald-700">
                    <th class="p-4">Nº</th>
                    <th class="p-4">Processo SEI</th>
                    <th class="p-4">Etapa</th>
                    <th class="p-4">Relator</th>
                    <th class="p-4">Presidente da Rodada</th>
                    <th class="p-4">Secretário(a)</th>
                    <th class="p-4">Abertura</th>
                    <th class="p-4">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 text-sm">
                @forelse($rodadas as $rodada)
                    @php
                        $processo = $rodada->etapa?->processo;
                    @endphp
                    <tr>
                        <td class="p-4 font-medium text-emerald-900">#{{ $rodada->id }}</td>
                        <td class="p-4 font-medium text-emerald-900">{{ $processo?->numero_sei ?? '-' }}</td>
                        <td class="p-4">{{ $rodada->etapa?->tipo_display ?? '-' }}</td>
                        <td class="p-4">{{ $processo?->relator?->nome ?? '-' }}</td>
                        <td class="p-4">{{ $rodada->presidente?->nome ?? '-' }}</td>
                        <td class="p-4">{{ $processo?->administrador?->nome ?? '-' }}</td>
                        <td class="p-4">{{ $rodada->data_abertura?->format('d/m/Y H:i') ?? '-' }}</td>
                        <td class="p-4">
                            <a href="{{ route('votacoes.minerva.votar-pagina', $rodada) }}"
                                class="inline-block text-white bg-emerald-600 px-4 py-1 rounded-lg font-bold hover:bg-emerald-700 transition-colors">
                                Votar
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="p-4 text-slate-500" colspan="8">Nenhuma votação aguardando o voto de Minerva.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection