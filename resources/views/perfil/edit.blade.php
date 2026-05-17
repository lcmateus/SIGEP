<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil - SIGEP</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-100 font-sans">
    <div class="flex min-h-screen bg-slate-100">
        @include('partials.asidebar', ['usuario' => 'usuario'])
        <main class="flex-1 bg-gray-200">
            @include('partials.header', [
                'titulo' => 'PERFIL',
                'usuario' => 'Usuário',
            ])
            <div class="p-10">
                <div class="bg-white border-2 border-emerald-600 rounded-2xl shadow-sm p-10 max-w-5xl mx-auto">
                    <form action="#" method="POST" enctype="multipart/form-data">
                        <div class="grid grid-cols-3 gap-8">
                            <div class="border-r border-slate-200 pr-8">
                                <h3 class="text-emerald-700 font-bold uppercase text-sm mb-6">
                                    Foto de Perfil
                                </h3>
                                <div class="flex flex-col items-center">
                                    <div
                                        class="w-40 h-40 rounded-full border-4 border-emerald-600 overflow-hidden shadow-md mb-6">
                                        <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png"
                                            alt="Foto de perfil" class="w-full h-full object-cover">
                                    </div>
                                    <label class="cursor-pointer">
                                        <input type="file" class="hidden">
                                        <div
                                            class="border border-emerald-600 text-emerald-700 px-5 py-3 rounded-lg font-bold hover:bg-emerald-50 transition-all">
                                            ⬆️ Trocar imagem
                                        </div>
                                    </label>
                                    <p class="text-sm text-slate-500 text-center mt-5">
                                        Formatos permitidos: JPG, PNG.<br>
                                        Tamanho máximo: 2MB.
                                    </p>
                                </div>
                            </div>
                            <div class="col-span-2">
                                <div class="space-y-6">
                                    <div>
                                        <label class="block text-sm font-bold text-emerald-700 uppercase mb-2">
                                            Nome Completo
                                        </label>
                                        <input type="text" 
                                            class="w-full border border-slate-300 rounded-lg p-4 outline-none focus:ring-2 focus:ring-emerald-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-emerald-700 uppercase mb-2">
                                            Email Institucional
                                        </label>
                                        <input type="email" 
                                            class="w-full border border-slate-300 rounded-lg p-4 outline-none focus:ring-2 focus:ring-emerald-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-emerald-700 uppercase mb-2">
                                            SIAPE
                                        </label>
                                        <input type="text" 
                                            class="w-full border border-slate-300 rounded-lg p-4 outline-none focus:ring-2 focus:ring-emerald-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-emerald-700 uppercase mb-2">
                                            Tipo de Membro
                                        </label>
                                        <input type="text"
                                            class="w-full border border-slate-300 rounded-lg p-4 outline-none focus:ring-2 focus:ring-emerald-500">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-end gap-4 mt-10 pt-8 border-t border-slate-200">
                            <button type="button"
                                class="border border-emerald-600 text-emerald-700 px-10 py-3 rounded-lg font-bold hover:bg-emerald-50 transition-all">
                                Editar
                            </button>
                            <button type="submit"
                                class="bg-emerald-600 text-white px-10 py-3 rounded-lg font-bold hover:bg-emerald-700 transition-all shadow-md">
                                💾 Salvar alterações
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>

</html>
