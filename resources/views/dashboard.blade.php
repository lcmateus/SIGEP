@extends('layouts.main_layout', [
    'titulo' => 'Dashboard',
])

@section('content')
    @if($usuario === 'admin')
        <div class="grid grid-cols-4 gap-6 mb-8">
            <div class="bg-emerald-700 p-6 rounded-xl shadow-sm border border-slate-200">
                <p class="text-white text-sm font-bold">Total de Processos</p>
                <p class="text-3xl font-bold text-white">{{ $totalProcessos }}</p>
            </div>
            <div class="bg-blue-600 p-6 rounded-xl shadow-sm border border-slate-200">
                <p class="text-white text-sm font-bold">Em Andamento</p>
                <p class="text-3xl font-bold text-white">{{ $emAndamento }}</p>
            </div>
            <div class="bg-yellow-600 p-6 rounded-xl shadow-sm border border-slate-200">
                <p class="text-white text-sm font-bold">Devolvidos</p>
                <p class="text-3xl font-bold text-white">{{ $devolvidos }}</p>
            </div>
            <div class="bg-green-600 p-6 rounded-xl shadow-sm border border-slate-200">
                <p class="text-white text-sm font-bold">Membros Ativos</p>
                <p class="text-3xl font-bold text-white">{{ $membrosAtivos }}</p>
            </div>
        </div>
    @else
        <div class="grid grid-cols-2 gap-6 mb-8">
            <div class="bg-blue-600 p-6 rounded-xl shadow-sm border border-slate-200">
                <p class="text-white text-sm font-bold">Processos que eu relato</p>
                <p class="text-3xl font-bold text-white">{{ $meusProcessos }}</p>
            </div>
            <div class="bg-emerald-700 p-6 rounded-xl shadow-sm border border-slate-200">
                <p class="text-white text-sm font-bold">Total de Processos</p>
                <p class="text-3xl font-bold text-white">{{ $totalProcessos }}</p>
            </div>
        </div>
    @endif

    <div class="bg-white shadow-sm mb-8 p-4 border-b border-slate-100 rounded-xl border">
        <h3 class="font-bold text-emerald-900 pb-3">Processos Recentes</h3>
        <table class="w-full text-left border-collapse mt-3">
            <thead class="bg-gray-200 text-slate-500 text-xs uppercase">
                <tr>
                    <th class="p-4 text-emerald-900">Numero SEI</th>
                    <th class="p-4 text-emerald-900">Admissão</th>
                    <th class="p-4 text-emerald-900">Devolução</th>
                    <th class="p-4 text-emerald-900">Relator</th>
                    <th class="p-4 text-emerald-900">Ações</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-slate-100">
                @forelse($processosRecentes as $processo)
                    <tr>
                        <td class="p-4 font-medium text-emerald-900">{{ $processo->numero_sei }}</td>
                        <td class="p-4">{{ $processo->data_admissao?->format('d/m/Y') ?? '-' }}</td>
                        <td class="p-4">{{ $processo->data_devolucao?->format('d/m/Y') ?? '-' }}</td>
                        <td class="p-4">{{ $processo->relator?->nome ?? '-' }}</td>
                        <td class="p-4 space-x-3">
                            <a class="text-white bg-blue-600 px-4 py-1 rounded-xl font-bold" href="{{ route('processos.show', $processo) }}">Detalhes</a>
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
