@extends('layouts.main_layout', [
    'titulo' => 'Próxima Etapa',
])

@section('content')
    @php
        $estilosEtapa = [
            \App\Models\Etapa::TIPO_PROCEDIMENTO_PRELIMINAR => 'bg-sky-50 hover:bg-sky-100 border-sky-400',
            \App\Models\Etapa::TIPO_ACPP => 'bg-yellow-50 hover:bg-yellow-100 border-yellow-400',
            \App\Models\Etapa::TIPO_PAE => 'bg-red-50 hover:bg-red-100 border-red-400',
        ];

        $descricoesEtapa = [
            \App\Models\Etapa::TIPO_PROCEDIMENTO_PRELIMINAR => 'Seguir para a etapa de Procedimento Preliminar (PP).',
            \App\Models\Etapa::TIPO_ACPP => 'Seguir para a etapa de Acordo de Conduta (ACPP).',
            \App\Models\Etapa::TIPO_PAE => 'Seguir para a etapa de Processo de Apuração (PAE).',
        ];
    @endphp

    <div class="max-w-2xl">
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6 mb-6">
            <div>
                <h2 class="font-bold text-emerald-900 text-2xl">Processo SEI {{ $processo->numero_sei }}</h2>
                <p class="text-sm text-slate-600 mt-2">
                    <span class="font-bold text-emerald-900">Relator:</span>
                    {{ $processo->relator?->nome ?? 'Não designado' }}
                    ·
                    <span class="font-bold text-emerald-900">Secretário(a):</span>
                    {{ $processo->administrador?->nome ?? 'Não informado' }}
                </p>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-bold text-emerald-900 mb-2">Qual a próxima etapa?</h3>
            <p class="text-sm text-slate-600 mb-6">
                A votação deste processo foi concluída. Escolha como o processo deve prosseguir.
            </p>

            <div class="grid gap-4">
                @foreach($opcoes as $tipo)
                    <form method="POST" action="{{ route('processos.proxima-etapa.store', $processo) }}">
                        @csrf
                        <input type="hidden" name="tipo" value="{{ $tipo }}">
                        <button type="submit"
                            class="w-full text-left border-2 rounded-xl p-5 transition-colors {{ $estilosEtapa[$tipo] ?? 'bg-slate-50 hover:bg-slate-100 border-slate-300' }}">
                            <span class="block text-lg font-bold text-emerald-900">{{ $tipo }}</span>
                            <span class="block text-sm text-slate-600 mt-1">
                                {{ $descricoesEtapa[$tipo] ?? 'Seguir para a etapa de '.$tipo.'.' }}
                            </span>
                        </button>
                    </form>
                @endforeach

                <form id="form-devolver-secretario" method="POST" action="{{ route('processos.devolver-secretario', $processo) }}">
                    @csrf
                    @method('PUT')
                </form>
                <button type="button"
                    data-confirm-form="form-devolver-secretario"
                    data-confirm-titulo="Devolver ao(à) Secretário(a)?"
                    data-confirm-mensagem="O processo será devolvido ao(à) Secretário(a)."
                    data-confirm-botao="Sim, devolver"
                    class="w-full text-left bg-slate-100 hover:bg-slate-200 border-2 border-slate-300 rounded-xl p-5 transition-colors">
                    <span class="block text-lg font-bold text-emerald-900">Devolver ao(à) Secretário(a)</span>
                    <span class="block text-sm text-slate-600 mt-1">Encaminhar o processo de volta ao(à) Secretário(a).</span>
                </button>

                <a href="{{ route('processos.show', $processo) }}"
                    class="inline-block text-center bg-slate-200 hover:bg-slate-300 text-emerald-900 text-sm font-bold px-5 py-2 rounded-md">
                    Voltar
                </a>
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
                <button type="button" id="modal-confirmar" class="font-bold py-2 px-4 rounded-lg text-white"></button>
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

                document.querySelectorAll('[data-confirm-titulo]').forEach(function (btn) {
                    btn.addEventListener('click', abrirConfirmacao);
                });
            })();
        </script>
    @endpush
@endsection
