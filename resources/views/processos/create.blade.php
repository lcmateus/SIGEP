@extends('layouts.main_layout', [
    'titulo' => 'Criar processo',
])

@section('content')
    <form action="{{ route('processos.store') }}" method="POST" class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 space-y-6">
        @csrf

        <div>
            <label class="font-bold text-emerald-900 text-xl">Titulo</label>
            <input type="text" name="titulo" value="{{ old('titulo') }}" placeholder="Digite o titulo da votacao"
                class="w-full border border-slate-300 p-3 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none mt-3">
            @error('titulo') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="font-bold text-emerald-900 text-xl">Descricao</label>
            <textarea name="descricao" rows="5" placeholder="Descreva os detalhes e informacoes relevantes"
                class="w-full border border-slate-300 p-3 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none resize-none mt-3">{{ old('descricao') }}</textarea>
            @error('descricao') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div class="flex flex-col">
                <label class="text-sm font-medium text-slate-700">Data de Inicio</label>
                <input type="datetime-local" name="data_inicio" value="{{ old('data_inicio') }}"
                    class="border border-slate-300 p-3 mt-1 rounded-lg outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div class="flex flex-col">
                <label class="text-sm font-medium text-slate-700">Data de Termino</label>
                <input type="datetime-local" name="data_fim" value="{{ old('data_fim') }}"
                    class="border border-slate-300 p-3 mt-1 rounded-lg outline-none focus:ring-2 focus:ring-emerald-500">
                @error('data_fim') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-emerald-600 text-white px-10 py-3 rounded-lg font-bold text-lg hover:bg-emerald-700 transition-all shadow-md">
                CRIAR VOTACAO
            </button>
        </div>
    </form>
@endsection
