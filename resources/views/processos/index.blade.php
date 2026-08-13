@extends('layouts.main_layout', [
    'titulo' => 'Processos',
])

@section('content')
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-sm font-bold text-emerald-700 uppercase">Lista de Processos</h3>
            @if(auth()->user()?->role === 'admin')
                <a href="{{ route('processos.create') }}" class="bg-emerald-600 text-white font-bold py-2 px-4 rounded-lg">Novo processo</a>
            @endif
        </div>

        <table class="w-full border-collapse">
            <thead class="bg-slate-100">
                <tr class="text-left text-sm text-emerald-700">
                    <th class="p-4">Titulo</th>
                    <th class="p-4">Status</th>
                    <th class="p-4">Data fim</th>
                    <th class="p-4">Acoes</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 text-sm">
                @forelse($processos as $processo)
                    <tr>
                        <td class="p-4">{{ $processo->titulo }}</td>
                        <td class="p-4">{{ ucfirst($processo->status) }}</td>
                        <td class="p-4">{{ $processo->data_fim ? $processo->data_fim->format('d/m/Y H:i') : '-' }}</td>
                        <td class="p-4">
                            <a class="text-blue-700 font-bold" href="{{ route('processos.show', $processo) }}">Detalhes</a>
                            @if(auth()->user()?->role === 'membro' && $processo->status === 'ativa')
                                <a class="ml-4 text-emerald-700 font-bold" href="{{ route('processos.votar', $processo) }}">Votar</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="p-4 text-slate-500" colspan="4">Nenhum processo cadastrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
