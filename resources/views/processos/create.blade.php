

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
    @include('partials.asidebar', ['usuario' => 'Admin'])
    <main class="flex-1 bg-gray-200">
        @include('partials.header', ['titulo' => 'CRIAR VOTAÇÃO', 'usuario' => 'Admin'])

        <form action="#" method="POST" class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 m-8">
            <div>
                
                <div class = "ml-3 mr-3 mb-3">
                    <label class="font-bold text-emerald-900 p-2 text-xl">Título</label>
                    <input type="text" placeholder="Digite o título da votação" 
                            class="w-full border border-slate-300 p-3 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none mt-3 hover:border-emerald-500">
                </div>

                <div class = "m-3">
                    <label class="font-bold text-emerald-900 p-2 text-xl">Descrição</label>
                    <textarea rows="5" placeholder="Descreva os detalhes e as informações relevantes para a votação" 
                                class="w-full border border-slate-300 p-3 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none resize-none mt-3 hover:border-emerald-500"></textarea>
                </div>

                <label class="font-bold text-emerald-900 p-2 text-xl m-3">Documentos relacionados</label>

                <div class = "border border-slate-300 p-3 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none resize-none m-3">
                    <div class="border-2 border border-slate-300 rounded-xl p-8 text-center hover:border-emerald-500 transition-colors cursor-pointer bg-slate-50 m-3 mt-1">
                        <div class="flex flex-col items-center">
                            <svg class="w-12 h-12 text-slate-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            <p class="text-sm text-slate-600 font-medium">Arraste e solte os arquivos aqui ou clique para fazer upload</p>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <div class="flex items-center justify-between bg-slate-100 p-3 m-3 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <span class="bg-red-500 text-white text-[10px] font-bold px-2 py-1 rounded">PDF</span>
                                <span class="text-sm text-slate-700">documento1.pdf</span>
                            </div>
                            <button class="text-slate-400 hover:text-red-600">✕</button>
                        </div>
                        <div class="flex items-center justify-between bg-slate-100 p-3 m-3 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <span class="bg-green-600 text-white text-[10px] font-bold px-2 py-1 rounded">XLSX</span>
                                <span class="text-sm text-slate-700">orçamento.xlsx</span>
                            </div>
                            <button class="text-slate-400 hover:text-red-600">✕</button>
                        </div>
                    </div>

                </div>
                
                <p class="text-slate-500 m-3">*Formatos permitidos: PDF, DOCS, XLSX, PNG, JPEG. Tamanho máximo: 100MB</p>
            </div>

            <div class = "m-3">
                <label class="font-bold text-emerald-900 p-2 text-xl">Período de votação</label>

                <div class="grid grid-cols-2 gap-4">

                    <div class="flex flex-col">
                        <label class="text-sm font-medium text-slate-700">Data de Início</label>
                        <input type="datetime-local" id="inicio" class="border border-slate-300 p-3 mt-1 rounded-lg outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="flex flex-col">
                        <label class="text-sm font-medium text-slate-700">Data de Término</label>
                        <input type="datetime-local" id="fim" class="border border-slate-300 p-3 mt-1 rounded-lg outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

             </div> 

            <div class="mt-8 flex justify-end">
                <button type="submit" class="bg-emerald-600 text-white px-10 py-3 rounded-lg font-bold text-lg hover:bg-emerald-700 transition-all shadow-md">
                    CRIAR VOTAÇÃO
                </button>
            </div>
        </form>
    </main>
</div>

</body>
</html>


