<main class="flex-1 bg-gray-200">
    @include('partials.header', ['titulo' => 'PAINEL DO ADMINISTRADOR', 'usuario' => 'Admin'])
    <div class="grid grid-cols-4 gap-6 m-8">
        @if($usuario == 'Admin')
            <div class="bg-emerald-700 p-6 rounded-xl shadow-sm border border-slate-200">
                <p class="text-white text-sm font-bold">Total de Usuários</p>
                <p class="text-3xl font-bold text-white">120</p>
            </div>
            <div class="bg-green-600 p-6 rounded-xl shadow-sm border border-slate-200">
                <p class="text-white text-sm font-bold">Votações Ativas</p>
                <p class="text-3xl font-bold text-white">05</p>
            </div>
            <div class="bg-yellow-600 p-6 rounded-xl shadow-sm border border-slate-200">
                <p class="text-white text-sm font-bold">Votações Encerradas</p>
                <p class="text-3xl font-bold text-white">12</p>
            </div>
            <div class="bg-red-600 p-6 rounded-xl shadow-sm border border-slate-200">
                <p class="text-white text-sm font-bold">Total de Votos</p>
                <p class="text-3xl font-bold text-white">350</p>
            </div>
        @else
            <div class="bg-green-600 p-6 rounded-xl shadow-sm border border-slate-200">
                <p class="text-white text-sm font-bold">Votações disponíveis</p>
                <p class="text-3xl font-bold text-white">05</p>
            </div>
            <div class="bg-blue-600 p-6 rounded-xl shadow-sm border border-slate-200">
                <p class="text-white text-sm font-bold">Votações que participei</p>
                <p class="text-3xl font-bold text-white">03</p>
            </div>
        @endif
        
    </div>

    <div class="bg-white shadow-sm m-8 p-4 border-b border-slate-100">
        <label class="font-bold text-emerald-900 pb-3">Votações Recentes</label>
        <hr>
        <table class="w-full text-left border-collapse mt-3">
            <thead class="bg-gray-200 text-slate-500 text-xs uppercase">
                <tr>
                    <th class="p-4 text-emerald-900">Título</th>
                    <th class="p-4 text-emerald-900">Status</th>
                    <th class="p-4 text-emerald-900">Ações</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-slate-100">
                <tr>
                    <td class="p-4 font-medium text-emerald-900">Reforma do Auditório</td>
                    <td class="p-4"><span class="bg-green-600 text-white px-4 py-1 font-bold rounded-xl">Ativa</span></td>
                    <td class="p-4 space-x-3">
                        @if($usuario == 'Admin')
                            <button class="text-white bg-blue-600 px-4 py-1 rounded-xl font-bold hover:underline">Editar</button>
                            <button class="text-white bg-green-600 px-4 py-1 rounded-xl font-bold hover:underline">Ver Resultados</button>
                            <button class="text-white bg-yellow-600 px-4 py-1 rounded-xl font-bold hover:underline">Encerrar</button>
                        @else
                            <button class="text-white bg-blue-600 px-4 py-1 rounded-xl font-bold hover:underline">Detalhes</button>
                            <button class="text-white bg-green-600 px-4 py-1 rounded-xl font-bold hover:underline">Votar</button>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</main>
