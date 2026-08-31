@extends('layouts.main_layout', [
    'titulo' => 'Votações Abertas',
])

@section('content')
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-sm font-bold text-emerald-700 uppercase">Votações Abertas</h3>
        </div>

        <table class="w-full border-collapse">
            <thead class="bg-slate-100">
                <tr class="text-left text-sm text-emerald-700">
                    <th class="p-4">Nº</th>
                    <th class="p-4">Processo SEI</th>
                    <th class="p-4">Etapa</th>
                    <th class="p-4">Relator</th>
                    <th class="p-4">Abertura</th>
                    <th class="p-4">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 text-sm">
                @forelse($rodadas as $rodada)
                    @php
                        $processo = $rodada->etapa?->processo;
                    @endphp
                    <tr>
                        <td class="p-4 font-medium text-emerald-900">#{{ $rodada->id }}</td>
                        <td class="p-4 font-medium text-emerald-900">{{ $processo?->numero_sei ?? '-' }}</td>
                        <td class="p-4">{{ $rodada->etapa?->tipo_display ?? '-' }}</td>
                        <td class="p-4">{{ $processo?->relator?->nome ?? '-' }}</td>
                        <td class="p-4">{{ $rodada->data_abertura?->format('d/m/Y H:i') ?? '-' }}</td>
                        <td class="p-4">
                            <button type="button" data-abrir-detalhe="#detalhe-aberta-{{ $loop->index }}"
                                class="text-white bg-blue-600 px-4 py-1 rounded-lg font-bold hover:bg-blue-700 transition-colors">
                                Detalhes
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="p-4 text-slate-500" colspan="6">Nenhuma votação aberta.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @foreach($rodadas as $rodada)
        @php
            $processo = $rodada->etapa?->processo;
        @endphp
        <div id="detalhe-aberta-{{ $loop->index }}" class="detalhe-overlay hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-xl max-w-3xl w-full max-h-[85vh] overflow-y-auto p-6">
                @if(!$processo)
                    <p class="text-sm text-slate-500 py-3">Processo associado não encontrado.</p>
                    <button type="button" data-fechar-detalhe
                        class="text-slate-400 hover:text-slate-700 text-xl font-bold leading-none">X</button>
                @else
                    <div class="flex items-start justify-between gap-4 mb-5">
                        <div>
                            <h2 class="font-bold text-emerald-900 text-2xl">Processo SEI {{ $processo->numero_sei }}</h2>
                            <p class="text-sm text-slate-600 mt-1">
                                <span class="font-bold text-emerald-900">Relator:</span>
                                {{ $processo->relator?->nome ?? 'Nao designado' }}
                                ·
                                <span class="font-bold text-emerald-900">Secretário(a):</span>
                                {{ $processo->administrador?->nome ?? 'Nao informado' }}
                            </p>
                            <p class="text-sm text-slate-500 mt-1">
                                Admissao: {{ $processo->data_admissao?->format('d/m/Y') ?? 'Nao informado' }}
                            </p>
                        </div>
                        <button type="button" data-fechar-detalhe
                            class="text-slate-400 hover:text-slate-700 text-xl font-bold leading-none">X</button>
                    </div>

                    <div class="mt-6">
                        <h3 class="font-bold text-emerald-900 text-lg mb-3">Datas da Rodada de Votação</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-emerald-900 mb-1">Data de Início</label>
                                <input type="date" value="{{ $rodada->data_abertura?->format('Y-m-d') ?? '' }}" readonly
                                    class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm bg-slate-100 focus:outline-none">
                            </div>
                            <div>
                                <label for="encerramento-{{ $loop->index }}" class="block text-sm font-bold text-emerald-900 mb-1">Data de Encerramento</label>
                                <input type="date" id="encerramento-{{ $loop->index }}"
                                    value="{{ $rodada->data_encerramento?->format('Y-m-d') ?? '' }}"
                                    class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            </div>
                        </div>
                        <div class="flex justify-end mt-4">
                            <button type="button" data-abrir-confirmacao
                                data-titulo="Confirmar alteração?"
                                data-mensagem="A nova data de encerramento será salva."
                                data-alvo="form-encerramento-{{ $loop->index }}"
                                class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded-lg transition-colors">
                                Confirmar
                            </button>
                        </div>

                        <form id="form-encerramento-{{ $loop->index }}" action="{{ route('votacoes.atualizar-encerramento', $rodada) }}" method="POST" class="hidden">
                            @csrf
                            @method('PUT')
                            <input type="hidden" id="encerramento-input-{{ $loop->index }}" name="data_encerramento" value="">
                        </form>
                    </div>
                @endif
            </div>
        </div>
    @endforeach

    <div id="modal-confirmacao" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
            <h3 id="modal-titulo" class="text-lg font-bold text-emerald-900 mb-2"></h3>
            <p id="modal-mensagem" class="text-sm text-slate-600 mb-6"></p>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="fecharConfirmacao()"
                    class="bg-slate-200 hover:bg-slate-300 text-emerald-900 font-bold py-2 px-4 rounded-lg">
                    Cancelar
                </button>
                <button type="button" id="modal-confirmar" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded-lg">
                    Confirmar
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        (function () {
            var modalConfirmacao = document.getElementById('modal-confirmacao');
            var formAlvo = null;

            document.addEventListener('click', function (e) {
                var abrir = e.target.closest('[data-abrir-detalhe]');
                if (abrir) {
                    var alvo = document.querySelector(abrir.getAttribute('data-abrir-detalhe'));
                    if (alvo) alvo.classList.remove('hidden');
                    return;
                }

                var fechar = e.target.closest('[data-fechar-detalhe]');
                if (fechar) {
                    fechar.closest('.detalhe-overlay').classList.add('hidden');
                    return;
                }

                var btnConfirmacao = e.target.closest('[data-abrir-confirmacao]');
                if (btnConfirmacao) {
                    var titulo = btnConfirmacao.getAttribute('data-titulo') || 'Confirmar';
                    var mensagem = btnConfirmacao.getAttribute('data-mensagem') || 'Deseja continuar?';
                    var formId = btnConfirmacao.getAttribute('data-alvo');
                    var index = btnConfirmacao.getAttribute('data-alvo').replace(/[^0-9]/g, '');

                    var inputData = document.getElementById('encerramento-' + index);
                    var inputHidden = document.getElementById('encerramento-input-' + index);

                    if (!inputData || !inputHidden) return;

                    inputHidden.value = inputData.value;
                    formAlvo = formId ? document.getElementById(formId) : null;

                    document.getElementById('modal-titulo').textContent = titulo;
                    document.getElementById('modal-mensagem').textContent = mensagem;
                    modalConfirmacao.classList.remove('hidden');
                }
            });

            window.fecharConfirmacao = function () {
                modalConfirmacao.classList.add('hidden');
                formAlvo = null;
            };

            document.getElementById('modal-confirmar').addEventListener('click', function () {
                modalConfirmacao.classList.add('hidden');
                if (formAlvo) {
                    formAlvo.submit();
                }
                formAlvo = null;
            });
        })();
    </script>
    @endpush
@endsection
