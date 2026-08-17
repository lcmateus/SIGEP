@extends('layouts.main_layout', [
    'titulo' => 'Dashboard',
])

@section('content')
    @if($usuario === 'admin')
        <div class="grid grid-cols-4 gap-6 mb-8">
            <div class="bg-emerald-700 p-6 rounded-xl shadow-sm border border-slate-200">
                <p class="text-white text-sm font-bold">Total de Usuarios</p>
                <p class="text-3xl font-bold text-white">{{ $totalUsuarios }}</p>
            </div>
            <div class="bg-green-600 p-6 rounded-xl shadow-sm border border-slate-200">
                <p class="text-white text-sm font-bold">Votacoes Ativas</p>
                <p class="text-3xl font-bold text-white">{{ $votacoesAtivas }}</p>
            </div>
            <div class="bg-yellow-600 p-6 rounded-xl shadow-sm border border-slate-200">
                <p class="text-white text-sm font-bold">Votacoes Encerradas</p>
                <p class="text-3xl font-bold text-white">{{ $votacoesEncerradas }}</p>
            </div>
            <div class="bg-red-600 p-6 rounded-xl shadow-sm border border-slate-200">
                <p class="text-white text-sm font-bold">Total de Votos</p>
                <p class="text-3xl font-bold text-white">{{ $totalVotos }}</p>
            </div>
        </div>
    @else
        <div class="grid grid-cols-2 gap-6 mb-8">
            <div class="bg-green-600 p-6 rounded-xl shadow-sm border border-slate-200">
                <p class="text-white text-sm font-bold">Votacoes disponiveis</p>
                <p class="text-3xl font-bold text-white">{{ $votacoesDisponiveis }}</p>
            </div>
            <div class="bg-blue-600 p-6 rounded-xl shadow-sm border border-slate-200">
                <p class="text-white text-sm font-bold">Votacoes realizadas</p>
                <p class="text-3xl font-bold text-white">{{ $votacoesRealizadas }}</p>
            </div>
        </div>
    @endif

    <div class="bg-white shadow-sm mb-8 p-4 border-b border-slate-100">
        <h3 class="font-bold text-emerald-900 pb-3">Votacoes Recentes</h3>
        <table class="w-full text-left border-collapse mt-3">
            <thead class="bg-gray-200 text-slate-500 text-xs uppercase">
                <tr>
                    <th class="p-4 text-emerald-900">Titulo</th>
                    <th class="p-4 text-emerald-900">Status</th>
                    <th class="p-4 text-emerald-900">Acoes</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-slate-100">
                @forelse($processosRecentes as $processo)
                    <tr>
                        <td class="p-4 font-medium text-emerald-900">{{ $processo->titulo }}</td>
                        <td class="p-4">{{ ucfirst($processo->status) }}</td>
                        <td class="p-4 space-x-3">
                            <a class="text-white bg-blue-600 px-4 py-1 rounded-xl font-bold" href="{{ route('processos.show', $processo) }}">Detalhes</a>
                            @if($usuario === 'membro' && $processo->status === 'ativa')
                                <a class="text-white bg-green-600 px-4 py-1 rounded-xl font-bold" href="{{ route('processos.votar', $processo) }}">Votar</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="p-4 text-slate-500" colspan="3">Nenhum processo cadastrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
