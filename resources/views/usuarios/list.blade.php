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
            <p class="text-sm text-slate-500">Usuarios pendentes de autorizacao</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <h3 class="text-sm font-bold text-emerald-700 uppercase mb-5">Lista de Usuarios</h3>
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead class="bg-slate-100">
                    <tr class="text-left text-sm text-emerald-700">
                        <th class="p-4">Nome completo</th>
                        <th class="p-4">E-mail institucional</th>
                        <th class="p-4">SIAPE</th>
                        <th class="p-4">Tipo</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-center">Acoes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-sm">
                    @forelse($usuarios as $usuario)
                        <tr>
                            <td class="p-4">{{ $usuario->nome }}</td>
                            <td class="p-4">{{ $usuario->email }}</td>
                            <td class="p-4">{{ $usuario->siape }}</td>
                            <td class="p-4">{{ $usuario->is_presidente ? 'Presidente' : 'Membro' }}</td>
                            <td class="p-4">{{ $usuario->data_ativacao ? 'Ativo' : 'Pendente' }}</td>
                            <td class="p-4">
                                <div class="flex justify-center gap-2">
                                    @if(! $usuario->data_ativacao)
                                        <form method="POST" action="{{ route('usuarios.approve', $usuario) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="border border-emerald-500 text-emerald-600 px-3 py-1 rounded-lg hover:bg-emerald-50">
                                                Aprovar
                                            </button>
                                        </form>
                                    @endif
                                    @if(! $usuario->is_presidente)
                                        <form method="POST" action="{{ route('usuarios.destroy', $usuario) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="border border-red-500 text-red-500 px-3 py-1 rounded-lg hover:bg-red-50">
                                                Excluir
                                            </button>
                                        </form>
                                    @endif
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
@endsection
