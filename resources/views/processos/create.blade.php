@extends('layouts.main_layout', [
    'titulo' => 'Criar Processo',
])

@section('content')
    <div class="max-w-4xl">
        <h3 class="text-sm font-bold text-emerald-700 uppercase mb-5">Novo Processo</h3>

        <form action="{{ route('processos.store') }}" method="POST" class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 space-y-6">
            @csrf

            <div>
                <label for="numero_sei" class="font-bold text-emerald-900 text-xl">Numero SEI</label>
                <input type="text" id="numero_sei" name="numero_sei" value="{{ old('numero_sei') }}" placeholder="Digite o numero SEI do processo" required
                    class="w-full border border-slate-300 p-3 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none mt-3">
                @error('numero_sei') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-4 text-sm text-emerald-800">
                O relator será designado automaticamente pelo sistema, priorizando o membro com menos processos atribuídos.
                A data de admissão será registrada como hoje.
            </div>

            <div class="flex items-center justify-between pt-2">
                <a href="{{ route('processos.index') }}" class="text-slate-500 font-bold hover:text-slate-700 hover:underline">Cancelar</a>
                <button type="submit" class="bg-emerald-600 text-white px-10 py-3 rounded-lg font-bold text-lg hover:bg-emerald-700 transition-all shadow-md">
                    CRIAR PROCESSO
                </button>
            </div>
        </form>
    </div>
@endsection
