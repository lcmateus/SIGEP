@extends('layouts.main_layout', [
    'titulo' => 'Usuarios',
])

@section('content')
    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <p class="text-sm font-bold text-emerald-700">Administradores</p>
            <h2 class="text-4xl font-bold text-emerald-700">{{ $totalAdmins }}</h2>
            <p class="text-sm text-slate-500">Total de administradores cadastrados</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <p class="text-sm font-bold text-orange-500">Aguardando autorizacao</p>
            <h2 class="text-4xl font-bold text-orange-500">{{ $totalPendentes }}</h2>
            <p class="text-sm text-slate-500">Usuários pendentes de autorização</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <h3 class="text-sm font-bold text-emerald-700 uppercase mb-5">Lista de Usuários</h3>
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead class="bg-slate-100">
                    <tr class="text-left text-sm text-emerald-700">
                        <th class="p-4">Nome completo</th>
                        <th class="p-4">E-mail institucional</th>
                        <th class="p-4">SIAPE</th>
                        <th class="p-4">Tipo</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-center">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-sm">
                    @forelse($usuarios as $usuario)
                        <tr>
                            <td class="p-4">{{ $usuario->nome }}</td>
                            <td class="p-4">{{ $usuario->email }}</td>
                            <td class="p-4">{{ $usuario->siape }}</td>
                            <td class="p-4">{{ in_array($usuario->siape, $siapePresidentes) ? 'Presidente' : 'Membro' }}</td>
                            <td class="p-4">{{ $usuario->data_ativacao ? 'Ativo' : 'Pendente' }}</td>
                            <td class="p-4">
                                <div class="flex justify-center gap-2">
                                    @if(! $usuario->data_ativacao)
                                        <form id="form-aprovar-{{ $usuario->id }}" method="POST" action="{{ route('usuarios.approve', $usuario) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="button"
                                                data-confirm-titulo="Aprovar cadastro"
                                                data-confirm-mensagem="Deseja aprovar o cadastro de {{ $usuario->nome }}?"
                                                data-confirm-botao="Aprovar"
                                                data-confirm-cor="emerald"
                                                data-confirm-form="form-aprovar-{{ $usuario->id }}"
                                                class="border border-emerald-500 text-emerald-600 px-3 py-1 rounded-lg hover:bg-emerald-50">
                                                Aprovar
                                            </button>
                                        </form>
                                    @endif
                                    <form id="form-excluir-{{ $usuario->id }}" method="POST" action="{{ route('usuarios.destroy', $usuario) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                            data-confirm-titulo="Excluir usuário"
                                            data-confirm-mensagem="Deseja excluir definitivamente o usuário {{ $usuario->nome }}?"
                                            data-confirm-botao="Excluir"
                                            data-confirm-cor="red"
                                            data-confirm-form="form-excluir-{{ $usuario->id }}"
                                            class="border border-red-500 text-red-500 px-3 py-1 rounded-lg hover:bg-red-50">
                                            Excluir
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="p-4 text-slate-500" colspan="6">Nenhum usuario cadastrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
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

                var cor = btn.getAttribute('data-confirm-cor') || 'emerald';
                btnConfirmar.className = cor === 'red'
                    ? 'bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg'
                    : 'bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded-lg';

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
