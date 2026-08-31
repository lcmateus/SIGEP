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
                        {{ $processo->relator?->nome ?? 'Nao designado' }}
                        ·
                        <span class="font-bold text-emerald-900">Secretário(a):</span>
                        {{ $processo->administrador?->nome ?? 'Nao informado' }}
                    </p>
                    <p class="text-sm text-slate-500 mt-1">
                        Admissao: {{ $processo->data_admissao?->format('d/m/Y') ?? 'Nao informado' }}
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

            <div class="mt-6 flex gap-3">
                <a href="{{ route('processos.index') }}" class="inline-block text-center bg-slate-200 hover:bg-slate-300 text-emerald-900 text-sm font-bold px-5 py-2 rounded-md">
                    Voltar
                </a>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6">
            @php
                $isAdmin = auth()->user() instanceof \App\Models\UsuarioAdministrador;
                $isMembro = auth()->user() instanceof \App\Models\UsuarioMembro;
                $isRelator = $isMembro && $processo->id_relator === auth()->user()->siape;
                $podeEscrever = $isAdmin || $isRelator;
            @endphp

            @include('processos.partials.detalhe-abas', [
                'uid' => 'show',
                'processo' => $processo,
                'documentosPorEtapa' => $documentosPorEtapa,
                'rodadas' => $rodadas,
                'somenteLeitura' => !$podeEscrever,
            ])

            @php
                $etapaAtual = $processo->etapa_atual;

                $juizoEmElaboracao = $processo->etapas->first(function ($etapa) {
                    return $etapa->tipo === \App\Models\Etapa::TIPO_JUIZO
                        && $etapa->status === \App\Models\Etapa::STATUS_EM_ELABORACAO;
                });

                $juizoFinalizada = $processo->etapas->first(function ($etapa) {
                    return $etapa->tipo === \App\Models\Etapa::TIPO_JUIZO
                        && $etapa->status === \App\Models\Etapa::STATUS_FINALIZADO;
                });

                $mostrarAceitar = $podeEscrever && $juizoEmElaboracao;
                $mostrarNovoCard = $podeEscrever
                    && $juizoFinalizada
                    && $etapaAtual
                    && $etapaAtual->status === \App\Models\Etapa::STATUS_EM_ELABORACAO;

                $ultimaEtapa = $processo->etapas->sortByDesc('ordem')->first();
                $precisaEscolherProxima = $podeEscrever
                    && $ultimaEtapa
                    && $ultimaEtapa->status === \App\Models\Etapa::STATUS_FINALIZADO
                    && !$processo->etapas->contains(fn ($e) => $e->status === \App\Models\Etapa::STATUS_EM_ELABORACAO);
            @endphp

            @if($precisaEscolherProxima)
                <div class="mt-6 p-4 bg-amber-50 border border-amber-300 rounded-lg">
                    <h3 class="text-lg font-bold text-emerald-900 mb-2">Escolher próxima etapa</h3>
                    <p class="text-sm text-slate-600 mb-4">
                        A votação deste processo foi concluída. Defina como o processo deve prosseguir.
                    </p>
                    <a href="{{ route('processos.proxima-etapa', $processo) }}"
                        class="inline-block bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-6 rounded-lg transition-colors">
                        Definir próxima etapa
                    </a>
                </div>
            @endif

            @if($mostrarAceitar)
                <div class="mt-6 p-4 bg-slate-50 border border-slate-200 rounded-lg">
                    <h3 class="text-lg font-bold text-emerald-900 mb-4">Aceitar Processo?</h3>
                    <div class="flex gap-3">
                        <form id="form-decisao-nao" action="{{ route('processos.aceitar.processar', $processo) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="decisao" value="nao">
                        </form>
                        <button type="button"
                            data-confirm-form="form-decisao-nao"
                            data-confirm-titulo="Devolver ao Secretário Geral?"
                            data-confirm-mensagem="O processo será devolvido ao Secretário Geral."
                            data-confirm-botao="Sim, Devolver"
                            class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded-lg transition-colors">
                            NÃO
                        </button>

                        <form id="form-decisao-sim" action="{{ route('processos.aceitar.processar', $processo) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="decisao" value="sim">
                        </form>
                        <button type="button"
                            data-confirm-form="form-decisao-sim"
                            data-confirm-titulo="Prosseguir para Procedimento Preliminar?"
                            data-confirm-mensagem="O processo seguirá para a etapa de Procedimento Preliminar."
                            data-confirm-botao="Sim, Prosseguir"
                            class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-6 rounded-lg transition-colors">
                            SIM
                        </button>
                    </div>
                </div>
            @endif

            @if($mostrarNovoCard)
                <div class="mt-6 p-4 bg-slate-50 border border-slate-200 rounded-lg">
                    <h3 class="text-lg font-bold text-emerald-900 mb-4">Qual o proximo passo?</h3>
                    <div class="flex gap-3">
                        <form id="form-devolver-secretario" action="{{ route('processos.devolver-secretario', $processo) }}" method="POST">
                            @csrf
                            @method('PUT')
                        </form>
                        <button type="button"
                            data-confirm-form="form-devolver-secretario"
                            data-confirm-titulo="Devolver a(o) Secretária(o)?"
                            data-confirm-mensagem="O processo será devolvido a(o) Secretária(o)."
                            data-confirm-botao="Sim, Devolver"
                            class="bg-slate-600 hover:bg-slate-700 text-white font-bold py-2 px-6 rounded-lg transition-colors">
                            Devolver a(o) Secretária(o)
                        </button>

                        <form id="form-iniciar-votacao" action="{{ route('processos.iniciar-votacao', $processo) }}" method="POST">
                            @csrf
                            @method('PUT')
                        </form>
                        <button type="button"
                            data-confirm-form="form-iniciar-votacao"
                            data-confirm-titulo="Iniciar Votação?"
                            data-confirm-mensagem="A etapa passará para Em Votação e uma rodada de votação será aberta."
                            data-confirm-botao="Sim, Iniciar"
                            class="bg-slate-600 hover:bg-slate-700 text-white font-bold py-2 px-6 rounded-lg transition-colors">
                            Iniciar Votação
                        </button>
                    </div>
                </div>
            @endif

            @php
                $mostrarAcoesAdmin = $isAdmin
                    && $etapaAtual
                    && $etapaAtual->status === \App\Models\Etapa::STATUS_DEVOLVIDO;
            @endphp

            @if($mostrarAcoesAdmin)
                <div class="mt-6 pt-6 border-t border-slate-200">
                    <h3 class="font-bold text-emerald-900 text-lg mb-4">Acoes do Administrador</h3>
                    <div class="flex flex-wrap gap-3">
                        <form id="form-admin-reativar" action="{{ route('processos.admin-acao', $processo) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="decisao" value="reativar">
                        </form>
                        <button type="button"
                            data-confirm-form="form-admin-reativar"
                            data-confirm-titulo="Devolver ao Relator?"
                            data-confirm-mensagem="O status da etapa voltará para Em Elaboração."
                            data-confirm-botao="Sim, Devolver"
                            class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold px-4 py-2 rounded-lg">
                            Devolver Ao Relator
                        </button>

                        <form id="form-admin-arquivar" action="{{ route('processos.admin-acao', $processo) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="decisao" value="arquivar">
                        </form>
                        <button type="button"
                            data-confirm-form="form-admin-arquivar"
                            data-confirm-titulo="Arquivar este processo?"
                            data-confirm-mensagem="O processo será arquivado."
                            data-confirm-botao="Sim, Arquivar"
                            class="bg-red-600 hover:bg-red-700 text-white text-sm font-bold px-4 py-2 rounded-lg">
                            Arquivar
                        </button>
                    </div>
                </div>
            @endif
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
                var titulo = document.getElementById('modal-titulo');
                var mensagem = document.getElementById('modal-mensagem');
                var btnConfirmar = document.getElementById('modal-confirmar');

                titulo.textContent = btn.getAttribute('data-confirm-titulo') || 'Confirmar';
                mensagem.textContent = btn.getAttribute('data-confirm-mensagem') || 'Deseja continuar?';
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

            document.querySelectorAll('[data-confirm-titulo]').forEach(function (btn) {
                btn.addEventListener('click', abrirConfirmacao);
            });
        })();
    </script>
    @endpush
@endsection
