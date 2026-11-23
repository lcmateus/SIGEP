@extends('layouts.main_layout', [
    'titulo' => 'Processo',
])

@section('content')
    <div class="max-w-4xl">
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6 mb-6">
            <div class="flex items-start justify-between gap-6">
                <div>
                    <h2 class="font-bold text-emerald-900 text-2xl">Processo SEI {{ $processo->numero_sei }}</h2>
                    <p class="text-sm text-slate-600 mt-2">
                        <span class="font-bold text-emerald-900">Relator:</span>
                        {{ $processo->relator?->nome ?? 'Não designado' }}
                        ·
                        <span class="font-bold text-emerald-900">Administrador responsavel:</span>
                        {{ $processo->administrador?->nome ?? 'Não informado' }}
                    </p>
                    <p class="text-sm text-slate-500 mt-1">
                        Admissao: {{ $processo->data_admissao?->format('d/m/Y') ?? 'Não informado' }}
                        @if($processo->data_devolucao)
                            · Devolucao: {{ $processo->data_devolucao->format('d/m/Y') }}
                        @endif
                    </p>
                </div>

                @if($processo->etapa_atual)
                    <span class="bg-emerald-600 px-5 py-1 rounded-md text-sm font-semibold tracking-wide text-white">
                        {{ $processo->etapa_atual->tipo_display }}
                    </span>
                @else
                    <span class="bg-slate-400 px-5 py-1 rounded-md text-sm font-semibold tracking-wide text-white">
                        Sem etapa
                    </span>
                @endif
            </div>

            @php
                $etapaAtual = $processo->etapas
                    ->sortByDesc('ordem')
                    ->first(fn ($etapa) => $etapa->status !== \App\Models\Etapa::STATUS_FINALIZADO);
                $mostrarAceitar = $etapaAtual && $etapaAtual->tipo === \App\Models\Etapa::TIPO_JUIZO;
            @endphp

            @if($mostrarAceitar)
                <div class="mt-6 p-4 bg-slate-50 border border-slate-200 rounded-lg">
                    <h3 class="text-lg font-bold text-emerald-900 mb-4">Aceitar Processo?</h3>
                    <div class="flex gap-3">
                        <button type="button" onclick="abrirConfirmacao('nao')"
                            class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded-lg transition-colors">
                            NÃO
                        </button>
                        <button type="button" onclick="abrirConfirmacao('sim')"
                            class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-6 rounded-lg transition-colors">
                            SIM
                        </button>
                    </div>
                </div>
            @endif

            <div class="mt-6 flex gap-3">
                <a href="{{ route('processos.meus') }}" class="inline-block text-center bg-slate-200 hover:bg-slate-300 text-emerald-900 text-sm font-bold px-5 py-2 rounded-md">
                    Voltar
                </a>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6">
            @include('processos.partials.detalhe-abas', [
                'uid' => 'aceitar',
                'processo' => $processo,
                'documentosPorEtapa' => $documentosPorEtapa,
                'rodadas' => $rodadas,
            ])
        </div>
    </div>

    <div id="modal-confirmacao" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
            <h3 id="modal-titulo" class="text-lg font-bold text-emerald-900 mb-2"></h3>
            <p id="modal-mensagem" class="text-sm text-slate-600 mb-6"></p>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="fecharConfirmacao()"
                    class="bg-slate-200 hover:bg-slate-300 text-emerald-900 font-bold py-2 px-4 rounded-lg">
                    Cancelar
                </button>
                <button type="button" id="modal-confirmar"
                    class="font-bold py-2 px-4 rounded-lg text-white">
                </button>
            </div>
        </div>
    </div>

    <form id="form-aceitar" method="POST" action="{{ route('processos.aceitar.processar', $processo) }}">
        @csrf
        @method('PUT')
        <input type="hidden" name="decisao" id="input-decisao" value="">
    </form>

    @push('scripts')
    <script>
        function abrirConfirmacao(decisao) {
            var modal = document.getElementById('modal-confirmacao');
            var titulo = document.getElementById('modal-titulo');
            var mensagem = document.getElementById('modal-mensagem');
            var btnConfirmar = document.getElementById('modal-confirmar');
            var input = document.getElementById('input-decisao');

            input.value = decisao;

            if (decisao === 'nao') {
                titulo.textContent = 'Devolver ao Secretário Geral?';
                mensagem.textContent = 'O processo será devolvido.".';
                btnConfirmar.textContent = 'Sim, Devolver';
                btnConfirmar.className = 'bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg';
            } else {
                titulo.textContent = 'Prosseguir para Procedimento Preliminar?';
                mensagem.textContent = 'O processo seguirá para a etapa de Procedimento Preliminar.';
                btnConfirmar.textContent = 'Sim, Prosseguir';
                btnConfirmar.className = 'bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded-lg';
            }

            modal.classList.remove('hidden');
        }

        function fecharConfirmacao() {
            document.getElementById('modal-confirmacao').classList.add('hidden');
        }

        document.getElementById('modal-confirmar').addEventListener('click', function () {
            document.getElementById('form-aceitar').submit();
        });
    </script>
    @endpush
@endsection
