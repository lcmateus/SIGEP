@extends('layouts.main_layout', [
    'titulo' => 'Processos',
])

@section('content')
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-sm font-bold text-emerald-700 uppercase">Lista de Processos</h3>
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
                    <th class="p-4">Acoes</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 text-sm">
                @forelse($processos as $processo)
                    <tr>
                        <td class="p-4 font-medium text-emerald-900">{{ $processo->numero_sei }}</td>
                        <td class="p-4">{{ $processo->data_admissao?->format('d/m/Y') ?? '-' }}</td>
                        <td class="p-4">{{ $processo->data_devolucao?->format('d/m/Y') ?? '-' }}</td>
                        <td class="p-4">{{ $processo->relator?->nome ?? '-' }}</td>
                        <td class="p-4">
                            <a class="text-blue-700 font-bold" href="{{ route('processos.show', $processo) }}">Detalhes</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="p-4 text-slate-500" colspan="5">Nenhum processo cadastrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
