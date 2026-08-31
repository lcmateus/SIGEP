@extends('layouts.main_layout', [
    'titulo' => 'Meus Processos',
])

@section('content')
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-sm font-bold text-emerald-700 uppercase">Meus Processos</h3>
        </div>

        <table class="w-full border-collapse">
            <thead class="bg-slate-100">
                <tr class="text-left text-sm text-emerald-700">
                    <th class="p-4">Numero SEI</th>
                    <th class="p-4">Admissão</th>
                    <th class="p-4">Devolução</th>
                    <th class="p-4">Etapa Atual</th>
                    <th class="p-4">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 text-sm">
                @forelse($processos as $processo)
                    <tr>
                        <td class="p-4 font-medium text-emerald-900">{{ $processo->numero_sei }}</td>
                        <td class="p-4">{{ $processo->data_admissao?->format('d/m/Y') ?? '-' }}</td>
                        <td class="p-4">{{ $processo->data_devolucao?->format('d/m/Y') ?? '-' }}</td>
                        <td class="p-4">{{ $processo->etapa_atual?->tipo_display ?? 'Sem etapa' }}</td>
                        <td class="p-4">
                            <a class="text-white bg-blue-600 px-4 py-1 rounded-xl font-bold" href="{{ route('processos.show', $processo) }}">Detalhes</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="p-4 text-slate-500" colspan="5">Nenhum processo atribuído.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
