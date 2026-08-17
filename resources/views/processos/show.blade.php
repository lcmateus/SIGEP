@extends('layouts.main_layout', [
    'titulo' => 'Processo',
])

@section('content')
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6">
        <div class="flex items-start justify-between gap-6">
            <div>
                <h2 class="font-bold text-emerald-900 text-2xl mb-3">{{ $processo->titulo }}</h2>
                <p class="text-slate-600 leading-relaxed mb-6">{{ $processo->descricao }}</p>
                <p class="text-sm text-slate-500">
                    Encerramento:
                    {{ $processo->data_fim ? $processo->data_fim->format('d/m/Y H:i') : 'Nao informado' }}
                </p>
            </div>

            <span class="bg-green-600 px-5 py-1 rounded-md text-sm font-semibold tracking-wide text-white">
                {{ ucfirst($processo->status) }}
            </span>
        </div>

        @if(auth()->user()?->role === 'membro' && $processo->status === 'ativa')
            <a href="{{ route('processos.votar', $processo) }}" class="inline-block mt-6 text-center bg-blue-600 hover:bg-blue-800 text-white text-sm font-bold px-5 py-2 rounded-md">
                Votar
            </a>
        @endif
    </div>
@endsection
