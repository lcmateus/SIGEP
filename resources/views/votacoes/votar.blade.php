@extends('layouts.main_layout', [
    'titulo' => 'Votação',
])

@section('content')
    <div class="max-w-4xl">
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6 mb-6">
            <div class="flex items-start justify-between gap-6">
                <div>
                    <h2 class="font-bold text-emerald-900 text-2xl">Processo SEI {{ $processo->numero_sei }}</h2>
                    <p class="text-sm text-slate-600 mt-2">
                        <span class="font-bold text-emerald-900">Relator:</span>
                        {{ $processo->relator?->nome ?? 'Nao designado' }}
                        ·
                        <span class="font-bold text-emerald-900">Secretário(a):</span>
                        {{ $processo->administrador?->nome ?? 'Nao informado' }}
                    </p>
                    <p class="text-sm text-slate-500 mt-1">
                        Admissao: {{ $processo->data_admissao?->format('d/m/Y') ?? 'Nao informado' }}
                    </p>
                    <p class="text-sm text-slate-500 mt-1">
                        Votação #{{ $rodada->id }}
                        · Abertura: {{ $rodada->data_abertura?->format('d/m/Y H:i') ?? '-' }}
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

            <div class="mt-6 flex gap-3">
                <a href="{{ route('votacoes.disponiveis') }}" class="inline-block text-center bg-slate-200 hover:bg-slate-300 text-emerald-900 text-sm font-bold px-5 py-2 rounded-md">
                    Voltar
                </a>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6">
            @include('processos.partials.detalhe-abas', [
                'uid' => 'votar',
                'processo' => $processo,
                'documentosPorEtapa' => $documentosPorEtapa,
                'rodadas' => collect(),
            ])

            <div class="mt-6 pt-6 border-t border-slate-200">
                <h3 class="font-bold text-emerald-900 text-lg mb-4">Registro de Voto</h3>
                <div class="max-w-sm space-y-3">
                    <form id="form-aprova" action="{{ route('votacoes.votar.store', $rodada) }}" method="POST">
                        @csrf
                        <input type="hidden" name="opcao" value="aprova">
                        <button type="button"
                            data-confirm-form="form-aprova"
                            data-confirm-titulo="Registrar voto?"
                            data-confirm-mensagem="Confirmar seu voto: APROVO?"
                            data-confirm-botao="Confirmar Aprovo"
                            class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 px-4 rounded-lg transition-colors">
                            Aprovo
                        </button>
                    </form>

                    <form id="form-desaprova" action="{{ route('votacoes.votar.store', $rodada) }}" method="POST">
                        @csrf
                        <input type="hidden" name="opcao" value="desaprova">
                        <button type="button"
                            data-confirm-form="form-desaprova"
                            data-confirm-titulo="Registrar voto?"
                            data-confirm-mensagem="Confirmar seu voto: DESAPROVO?"
                            data-confirm-botao="Confirmar Desaprovo"
                            class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 px-4 rounded-lg transition-colors">
                            Desaprovo
                        </button>
                    </form>

                    <div>
                        <button type="button" id="btn-ressalva"
                            class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2.5 px-4 rounded-lg transition-colors">
                            Aprovo, com ressalva
                        </button>
                        <div id="ressalva-area" class="hidden mt-3">
                            <form id="form-ressalva" action="{{ route('votacoes.votar.store', $rodada) }}" method="POST">
                                @csrf
                                <input type="hidden" name="opcao" value="aprova com resalva">
                                <textarea name="justificativa" id="ressalva-texto" rows="3" placeholder="Escreva sua ressalva..."
                                    class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500"></textarea>
                                <div class="flex justify-end mt-2">
                                    <button type="button" id="btn-enviar-ressalva"
                                        data-confirm-form="form-ressalva"
                                        data-confirm-titulo="Registrar voto?"
                                        data-confirm-mensagem="Confirmar seu voto: APROVO, COM RESSALVA?"
                                        data-confirm-botao="Confirmar Voto"
                                        class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded-lg text-sm transition-colors">
                                        Enviar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <form id="form-abstencao" action="{{ route('votacoes.votar.store', $rodada) }}" method="POST">
                        @csrf
                        <input type="hidden" name="opcao" value="abstenho">
                        <button type="button"
                            data-confirm-form="form-abstencao"
                            data-confirm-titulo="Registrar voto?"
                            data-confirm-mensagem="Confirmar sua abstenção?"
                            data-confirm-botao="Confirmar Abstenção"
                            class="w-full bg-slate-500 hover:bg-slate-600 text-white font-bold py-2.5 px-4 rounded-lg transition-colors">
                            Abstenho
                        </button>
                    </form>
                </div>
            </div>
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
                <button type="button" id="modal-confirmar" class="font-bold py-2 px-4 rounded-lg text-white">
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        (function () {
            var modal = document.getElementById('modal-confirmacao');
            var formAlvo = null;

            function abrirConfirmacao(event) {
                var btn = event.currentTarget;
                document.getElementById('modal-titulo').textContent = btn.getAttribute('data-confirm-titulo') || 'Confirmar';
                document.getElementById('modal-mensagem').textContent = btn.getAttribute('data-confirm-mensagem') || 'Deseja continuar?';
                var btnConfirmar = document.getElementById('modal-confirmar');
                btnConfirmar.textContent = btn.getAttribute('data-confirm-botao') || 'Confirmar';
                btnConfirmar.className = 'bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded-lg';

                var formId = btn.getAttribute('data-confirm-form');
                formAlvo = formId ? document.getElementById(formId) : null;

                modal.classList.remove('hidden');
            }

            window.fecharConfirmacao = function () {
                modal.classList.add('hidden');
                formAlvo = null;
            };

            document.getElementById('modal-confirmar').addEventListener('click', function () {
                modal.classList.add('hidden');
                if (formAlvo) {
                    formAlvo.submit();
                }
                formAlvo = null;
            });

            document.querySelectorAll('[data-confirm-form], [data-confirm-titulo]').forEach(function (btn) {
                btn.addEventListener('click', abrirConfirmacao);
            });

            var btnRessalva = document.getElementById('btn-ressalva');
            var areaRessalva = document.getElementById('ressalva-area');

            if (btnRessalva && areaRessalva) {
                btnRessalva.addEventListener('click', function () {
                    areaRessalva.classList.toggle('hidden');
                });
            }
        })();
    </script>
    @endpush
@endsection
