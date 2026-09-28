@extends('layouts.main_layout', [
    'titulo' => 'Criar Processo',
])

@section('content')
    <div class="max-w-4xl">
        <h3 class="text-sm font-bold text-emerald-700 uppercase mb-5">Novo Processo</h3>

        <form action="{{ route('processos.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 space-y-6">
            @csrf

            <div>
                <label for="numero_sei" class="font-bold text-emerald-900 text-xl">Número SEI</label>
                <input type="text" id="numero_sei" name="numero_sei" value="{{ old('numero_sei') }}" placeholder="Digite o número SEI do processo" required
                    class="w-full border border-slate-300 p-3 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none mt-3">
                @error('numero_sei') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="arquivo" class="font-bold text-emerald-900 text-xl">Documento inicial (obrigatório)</label>
                <p class="text-sm text-slate-500 mt-1">Anexe o documento inicial do processo relacionado no SEI.</p>
                <input type="file" id="arquivo" name="arquivo" required
                    class="w-full text-sm text-slate-600 mt-3 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-bold file:bg-emerald-600 file:text-white hover:file:bg-emerald-700">
                @error('arquivo') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="tipo" class="font-bold text-emerald-900 text-xl">Tipo de documento</label>
                <select id="tipo" name="tipo" required
                    class="w-full border border-slate-300 rounded-lg px-3 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 mt-3">
                    <option value="pdf_sei" @selected(old('tipo', 'pdf_sei') === 'pdf_sei')>PDF do SEI</option>
                    <option value="relatorio_juizo" @selected(old('tipo') === 'relatorio_juizo')>Relatorio do Juízo</option>
                    <option value="relatorio_pp" @selected(old('tipo') === 'relatorio_pp')>Relatório do PP</option>
                    <option value="minuta_acpp" @selected(old('tipo') === 'minuta_acpp')>Minuta do ACPP</option>
                    <option value="versao_final_acpp" @selected(old('tipo') === 'versao_final_acpp')>Versão Final do ACPP</option>
                    <option value="versao_assinada_acpp" @selected(old('tipo') === 'versao_assinada_acpp')>Versão Assinada do ACPP</option>
                    <option value="diligencia" @selected(old('tipo') === 'diligencia')>Diligência</option>
                    <option value="prova_documental" @selected(old('tipo') === 'prova_documental')>Prova Documental</option>
                    <option value="prova_testemunhal" @selected(old('tipo') === 'prova_testemunhal')>Prova Testemunhal</option>
                    <option value="prova_pericial" @selected(old('tipo') === 'prova_pericial')>Prova Pericial</option>
                    <option value="relatorio_parcial_pae" @selected(old('tipo') === 'relatorio_parcial_pae')>Relatório Parcial do PAE</option>
                    <option value="defesa_investigado" @selected(old('tipo') === 'defesa_investigado')>Defesa do Investigado</option>
                    <option value="alegacoes_finais" @selected(old('tipo') === 'alegacoes_finais')>Alegações Finais</option>
                    <option value="reconsideracao" @selected(old('tipo') === 'reconsideracao')>Reconsideração</option>
                    <option value="outro" @selected(old('tipo') === 'outro')>Outro</option>
                </select>
                @error('tipo') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
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
