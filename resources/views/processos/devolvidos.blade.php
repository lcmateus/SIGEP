@extends('layouts.main_layout', [
    'titulo' => 'Processos Devolvidos',
])

@section('content')
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-sm font-bold text-emerald-700 uppercase">Processos Devolvidos</h3>
            @if(auth()->user() instanceof \App\Models\UsuarioAdministrador)
                <a href="{{ route('processos.create') }}" class="bg-emerald-600 text-white font-bold py-2 px-4 rounded-lg">Novo processo</a>
            @endif
        </div>

        <table class="w-full border-collapse">
            <thead class="bg-slate-100">
                <tr class="text-left text-sm text-emerald-700">
                    <th class="p-4">Numero SEI</th>
                    <th class="p-4">Admissao</th>
                    <th class="p-4">Devolucao</th>
                    <th class="p-4">Relator</th>
                    <th class="p-4">Etapa Devolvida</th>
                    <th class="p-4">Acoes</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 text-sm">
                @forelse($processos as $processo)
                    @php
                        $etapaDevolvida = $processo->etapas->firstWhere('status', \App\Models\Etapa::STATUS_DEVOLVIDO);
                    @endphp
                    <tr>
                        <td class="p-4 font-medium text-emerald-900">{{ $processo->numero_sei }}</td>
                        <td class="p-4">{{ $processo->data_admissao?->format('d/m/Y') ?? '-' }}</td>
                        <td class="p-4">{{ $processo->data_devolucao?->format('d/m/Y') ?? '-' }}</td>
                        <td class="p-4">{{ $processo->relator?->nome ?? '-' }}</td>
                        <td class="p-4">{{ $etapaDevolvida?->tipo_display ?? '-' }}</td>
                        <td class="p-4">
                            <a class="text-white bg-blue-600 px-4 py-1 rounded-xl font-bold" href="{{ route('processos.show', $processo) }}">Detalhes</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="p-4 text-slate-500" colspan="6">Nenhum processo devolvido.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
