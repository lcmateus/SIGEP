<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sucesso - SIGEP</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 font-sans">

    <div class="flex min-h-screen bg-slate-100">
    <aside class="w-64 bg-emerald-700 border-r border-slate-200 flex flex-col space-y-8">
        <div class = "bg-emerald-500 p-6 h-32 flex items-center">
            <div class="flex items-center space-x-3">
                <div class="grid grid-cols-3 gap-0.5 w-8">
                    <div class="h-2 w-2 bg-red-600"></div><div class="h-2 w-2 bg-green-600"></div><div class="h-2 w-2 bg-green-600"></div>
                    <div class="h-2 w-2 bg-green-600"></div><div class="h-2 w-2 bg-green-600"></div><div class="h-2 w-2 bg-green-600"></div>
                </div>
                <div>
                    <h1 class="font-bold text-xl leading-none">SIGEP</h1>
                    <p class="text-[10px] text-slate-500 uppercase">Gestão da Ética Pública</p>
                </div>
            </div>
        </div>

        <nav class="flex-1 space-y-2 text-sm font-medium">
            <a href="#" class="flex items-center p-3 bg-emerald-200 text-emerald-700">Dashboard</a>
            <a href="#" class="flex items-center p-3 text-white hover:bg-emerald-900">Votações</a>
            <a href="#" class="flex items-center p-3 text-white hover:bg-emerald-900">Criar votação</a>
            <a href="#" class="flex items-center p-3 text-white hover:bg-emerald-900">Usuários</a>
            <a href="#" class="flex items-center p-3 text-white hover:bg-emerald-900">Resultados</a>
            <hr>
            <a href="#" class="flex items-center p-3 text-white hover:bg-emerald-900">Configurações</a>
        </nav>
    </aside>

    <main class="flex-1 bg-gray-200">
        <header class="flex justify-between items-center p-8 mb-5 bg-white">
            <h2 class="text-2xl font-bold text-emerald-900">PAINEL DO ADMINISTRADOR</h2>
            <div class="flex items-center space-x-4">
                <button class="text-emerald-900 font-bold hover:underline">Admin</button>
                <span class="text-slate-400">|</span>
                <span class="text-left text-red-600 font-bold hover:underline">Sair</span>
            </div>
        </header>

        <div class="grid grid-cols-4 gap-6 m-8">
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
        </div>

        <div class="bg-white shadow-sm m-8 p-4 border-b border-slate-100">
            <h2 class="font-bold text-emerald-900 pb-3">Votações Recentes</h2>
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
                            <button class="text-white bg-blue-600 px-4 py-1 rounded-xl font-bold hover:underline">Editar</button>
                            <button class="text-white bg-green-600 px-4 py-1 rounded-xl font-bold hover:underline">Ver Resultados</button>
                            <button class="text-white bg-yellow-600 px-4 py-1 rounded-xl font-bold hover:underline">Encerrar</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </main>
</div>

</body>
</html>
