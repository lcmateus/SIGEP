@extends('layouts.main_layout', [
    'titulo' => 'Votações Minerva',
])

@section('content')
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-sm font-bold text-emerald-700 uppercase">Desempates (Voto de Minerva)</h3>
        </div>

        @unless($isPresidente)
            <p class="text-slate-500 text-sm">Não há Minerva disponível no momento para o usuário logado.</p>
        @elseif($rodadas->isEmpty())
            <p class="text-slate-500 text-sm">Nenhuma votação aguardando voto de Minerva.</p>
        @else
            <table class="w-full border-collapse">
                <thead class="bg-slate-100">
                    <tr class="text-left text-sm text-emerald-700">
                        <th class="p-4">Processo SEI</th>
                        <th class="p-4">Etapa</th>
                        <th class="p-4">Relator</th>
                        <th class="p-4">Abertura</th>
                        <th class="p-4">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-sm">
                    @foreach($rodadas as $rodada)
                        @php
                            $processo = $rodada->etapa?->processo;
                        @endphp
                        <tr>
                            <td class="p-4 font-medium text-emerald-900">{{ $processo?->numero_sei ?? '-' }}</td>
                            <td class="p-4">{{ $rodada->etapa?->tipo_display ?? '-' }}</td>
                            <td class="p-4">{{ $processo?->relator?->nome ?? '-' }}</td>
                            <td class="p-4">{{ $rodada->data_abertura?->format('d/m/Y H:i') ?? '-' }}</td>
                            <td class="p-4">
                                <form id="minerva-aprova-{{ $rodada->id }}" action="{{ route('votacoes.minerva.votar', $rodada) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="opcao" value="aprova">
                                    <button type="button"
                                        data-confirm-form="minerva-aprova-{{ $rodada->id }}"
                                        data-confirm-titulo="Voto de Minerva: Aprovar?"
                                        data-confirm-mensagem="Confirmar voto de Minerva APROVANDO o processo {{ $processo?->numero_sei }}?"
                                        data-confirm-botao="Confirmar Aprova"
                                        class="bg-green-600 hover:bg-green-700 text-white font-bold px-4 py-1 rounded-lg transition-colors">
                                        Aprovar
                                    </button>
                                </form>
                                <form id="minerva-reprova-{{ $rodada->id }}" action="{{ route('votacoes.minerva.votar', $rodada) }}" method="POST" class="inline mt-2">
                                    @csrf
                                    <input type="hidden" name="opcao" value="desaprova">
                                    <button type="button"
                                        data-confirm-form="minerva-reprova-{{ $rodada->id }}"
                                        data-confirm-titulo="Voto de Minerva: Reprovar?"
                                        data-confirm-mensagem="Confirmar voto de Minerva REPROVANDO o processo {{ $processo?->numero_sei }}?"
                                        data-confirm-botao="Confirmar Reprova"
                                        class="bg-red-600 hover:bg-red-700 text-white font-bold px-4 py-1 rounded-lg transition-colors">
                                        Reprovar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
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
