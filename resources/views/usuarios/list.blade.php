@extends('layouts/main_layout', [
    'title' => 'Usuários - SIGEP',
    'titulo' => 'USUÁRIOS',
    'usuario' => 'admin',
])
@section('content')
    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 flex items-center gap-5">
            <div class="w-16 h-16 rounded-full bg-emerald-100 flex items-center justify-center text-3xl">
                👥
            </div>
            <div>
                <p class="text-sm font-bold text-emerald-700">
                    Administradores
                </p>
                <h2 class="text-4xl font-bold text-emerald-700">
                    5
                </h2>
                <p class="text-sm text-slate-500">
                    Total de administradores cadastrados
                </p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 flex items-center gap-5">
            <div class="w-16 h-16 rounded-full bg-orange-100 flex items-center justify-center text-3xl">
                🕒
            </div>
            <div>
                <p class="text-sm font-bold text-orange-500">
                    Aguardando autorização
                </p>
                <h2 class="text-4xl font-bold text-orange-500">
                    12
                </h2>
                <p class="text-sm text-slate-500">
                    Usuários pendentes de autorização
                </p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 mb-6">
        <div class="grid grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Buscar usuário
                </label>
                <input type="text" placeholder="Digite o nome, email ou SIAPE"
                    class="w-full border border-slate-300 rounded-lg p-3 outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Tipo de membro
                </label>
                <select
                    class="w-full border border-slate-300 rounded-lg p-3 outline-none focus:ring-2 focus:ring-emerald-500">
                    <option>Todos</option>
                    <option>Titular</option>
                    <option>Suplente</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Status
                </label>
                <select
                    class="w-full border border-slate-300 rounded-lg p-3 outline-none focus:ring-2 focus:ring-emerald-500">
                    <option>Todos</option>
                    <option>Ativo</option>
                    <option>Pendente</option>
                    <option>Inativo</option>
                </select>
            </div>
            <div class="flex items-end">
                <button
                    class="w-full bg-emerald-600 text-white font-bold py-3 rounded-lg hover:bg-emerald-700 transition-all">
                    Filtrar
                </button>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <h3 class="text-sm font-bold text-emerald-700 uppercase mb-5">
            Lista de Usuários
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead class="bg-slate-100">
                    <tr class="text-left text-sm text-emerald-700">
                        <th class="p-4">Nome completo</th>
                        <th class="p-4">E-mail institucional</th>
                        <th class="p-4">SIAPE</th>
                        <th class="p-4">Tipo de membro</th>
                        <th class="p-4">Status</th>
                        <th class="p-4">Data de cadastro</th>
                        <th class="p-4 text-center">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-sm">
                    <tr class="hover:bg-slate-50">
                        <td class="p-4 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-200 flex items-center justify-center">
                                👨
                            </div>
                            João da Silva
                        </td>
                        <td class="p-4">
                            joao.silva@ifpi.edu.br
                        </td>
                        <td class="p-4">
                            1234567
                        </td>
                        <td class="p-4">
                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-lg text-xs font-bold">
                                Titular
                            </span>
                        </td>
                        <td class="p-4">
                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-lg text-xs font-bold">
                                Ativo
                            </span>
                        </td>
                        <td class="p-4">
                            10/03/2025
                        </td>
                        <td class="p-4">
                            <div class="flex justify-center gap-2">
                                <button
                                    class="border border-emerald-500 text-emerald-600 px-3 py-1 rounded-lg hover:bg-emerald-50">
                                    ✏️
                                </button>
                                <button class="border border-red-500 text-red-500 px-3 py-1 rounded-lg hover:bg-red-50">
                                    🗑️
                                </button>
                                <button
                                    class="border border-slate-300 text-slate-600 px-3 py-1 rounded-lg hover:bg-slate-100">
                                    ⋮
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr class="hover:bg-slate-50">
                        <td class="p-4 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-pink-200 flex items-center justify-center">
                                👩
                            </div>
                            Maria Oliveira
                        </td>
                        <td class="p-4">
                            maria.oliveira@ifpi.edu.br
                        </td>
                        <td class="p-4">
                            2345678
                        </td>
                        <td class="p-4">
                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-lg text-xs font-bold">
                                Titular
                            </span>
                        </td>
                        <td class="p-4">
                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-lg text-xs font-bold">
                                Ativo
                            </span>
                        </td>
                        <td class="p-4">
                            15/03/2025
                        </td>
                        <td class="p-4">
                            <div class="flex justify-center gap-2">
                                <button
                                    class="border border-emerald-500 text-emerald-600 px-3 py-1 rounded-lg hover:bg-emerald-50">
                                    ✏️
                                </button>
                                <button class="border border-red-500 text-red-500 px-3 py-1 rounded-lg hover:bg-red-50">
                                    🗑️
                                </button>
                                <button
                                    class="border border-slate-300 text-slate-600 px-3 py-1 rounded-lg hover:bg-slate-100">
                                    ⋮
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection
