@extends('layouts.main_layout', [
    'titulo' => 'Voto de Minerva',
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
                        ·
                        <span class="font-bold text-emerald-900">Presidente da Rodada:</span>
                        {{ $rodada->presidente?->nome ?? 'Nao informado' }}
                    </p>
                    <p class="text-sm text-slate-500 mt-1">
                        Admissao: {{ $processo->data_admissao?->format('d/m/Y') ?? 'Nao informado' }}
                    </p>
                    <p class="text-sm text-slate-500 mt-1">
                        Votação #{{ $rodada->id }}
                        · Abertura: {{ $rodada->data_abertura?->format('d/m/Y H:i') ?? '-' }}
                        · Encerramento: {{ $rodada->data_encerramento?->format('d/m/Y H:i') ?? '-' }}
                    </p>
                </div>

                @if($rodada->etapa)
                    <span class="bg-amber-500 px-5 py-1 rounded-md text-sm font-semibold tracking-wide text-white">
                        {{ $rodada->etapa->tipo_display }}
                    </span>
                @else
                    <span class="bg-slate-400 px-5 py-1 rounded-md text-sm font-semibold tracking-wide text-white">
                        Sem etapa
                    </span>
                @endif
            </div>

            <div class="mt-6 flex gap-3">
                <a href="{{ route('votacoes.minerva') }}" class="inline-block text-center bg-slate-200 hover:bg-slate-300 text-emerald-900 text-sm font-bold px-5 py-2 rounded-md">
                    Voltar
                </a>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6">
            @include('processos.partials.detalhe-abas', [
                'uid' => 'minerva-votar',
                'processo' => $processo,
                'documentosPorEtapa' => $documentosPorEtapa,
                'rodadas' => collect(),
                'somenteLeitura' => true,
            ])

            <div class="mt-6 pt-6 border-t border-slate-200">
                <h3 class="font-bold text-emerald-900 text-lg mb-4">Voto de Minerva</h3>
                <div class="max-w-sm space-y-3">
                    <form id="form-aprova" action="{{ route('votacoes.minerva.votar', $rodada) }}" method="POST">
                        @csrf
                        <input type="hidden" name="opcao" value="aprova">
                        <button type="button"
                            data-confirm-form="form-aprova"
                            data-confirm-titulo="Voto de Minerva: Aprovar"
                            data-confirm-mensagem="Confirmar voto de Minerva APROVANDO {{ $processo->numero_sei }}?"
                            data-confirm-botao="Confirmar Aprovar"
                            class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 px-4 rounded-lg transition-colors">
                            Aprovar
                        </button>
                    </form>

                    <form id="form-reprova" action="{{ route('votacoes.minerva.votar', $rodada) }}" method="POST">
                        @csrf
                        <input type="hidden" name="opcao" value="desaprova">
                        <button type="button"
                            data-confirm-form="form-reprova"
                            data-confirm-titulo="Voto de Minerva: Reprovar"
                            data-confirm-mensagem="Confirmar voto de Minerva REPROVANDO {{ $processo->numero_sei }}?"
                            data-confirm-botao="Confirmar Reprovar"
                            class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 px-4 rounded-lg transition-colors">
                            Reprovar
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

            document.querySelectorAll('[data-confirm-form]').forEach(function (btn) {
                btn.addEventListener('click', abrirConfirmacao);
            });
        })();
    </script>
    @endpush
@endsection