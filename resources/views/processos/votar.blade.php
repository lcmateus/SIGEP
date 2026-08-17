@extends('layouts.main_layout', [
    'title' => 'Votação - SIGEP',
    'titulo' => 'PROCESSOS / REFORMA AUDITÓRIO',
])

@section('content')
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-10">

        <h2 class="text-3xl font-bold text-emerald-900">
            Reforma do Auditório
        </h2>

        <p class="text-slate-500 font-semibold mb-8">
            Descrição
        </p>

        <div class="grid grid-cols-3 gap-6">

            <div class="col-span-2">

                <img src="https://images.unsplash.com/photo-1511578314322-379afb476865?q=80&w=1200" alt="Auditório"
                    class="w-full h-72 object-cover rounded-2xl border border-slate-300">

            </div>

            <div class="border border-slate-300 rounded-lg p-4">

                <h3 class="font-bold text-xl text-emerald-900 mb-4">
                    Documentos relacionados
                </h3>

                <div class="space-y-3">

                    <div class="flex justify-between items-center border rounded-lg p-2">

                        <div class="flex items-center gap-3">
                            <span class="text-red-500">📄</span>
                            <span>documento1.pdf</span>
                        </div>

                        <span class="bg-green-600 text-white text-xs px-3 py-1 rounded font-bold">
                            PDF
                        </span>

                    </div>

                    <div class="flex justify-between items-center border rounded-lg p-2">

                        <div class="flex items-center gap-3">
                            <span class="text-green-600">📊</span>
                            <span>orcamento.xlsx</span>
                        </div>

                        <span class="bg-green-600 text-white text-xs px-3 py-1 rounded font-bold">
                            XLSX
                        </span>

                    </div>

                </div>

            </div>

        </div>

        <div class="mt-8">

            <h3 class="text-3xl text-center font-bold text-emerald-900 mb-4">
                VOTAÇÃO
            </h3>

            <form>

                <div class="grid grid-cols-2 gap-6">

                    <div class="border border-slate-300 rounded-lg p-6">

                        <h4 class="text-2xl font-bold text-emerald-900">
                            Você é a favor da reforma do auditório?
                        </h4>

                        <p class="text-slate-400 font-semibold mt-4">
                            Obs: Seu voto é sigiloso e você só pode votar uma vez.
                            Escolha com atenção.
                        </p>

                    </div>

                    <div class="space-y-3">

                        <label class="block">
                            <input type="radio" name="voto" value="favor" class="hidden peer voto-option">

                            <div
                                class="border border-slate-300 rounded-lg p-3 cursor-pointer peer-checked:bg-green-600 peer-checked:text-white">
                                SIM, SOU A FAVOR
                            </div>
                        </label>

                        <label class="block">
                            <input type="radio" name="voto" value="contra" class="hidden peer voto-option">

                            <div
                                class="border border-slate-300 rounded-lg p-3 cursor-pointer peer-checked:bg-red-600 peer-checked:text-white">
                                NÃO, SOU CONTRA
                            </div>
                        </label>

                        <label class="block">
                            <input type="radio" name="voto" value="abstencao" class="hidden peer voto-option">

                            <div
                                class="border border-slate-300 rounded-lg p-3 cursor-pointer peer-checked:bg-slate-600 peer-checked:text-white">
                                PREFIRO ME ABSTER
                            </div>
                        </label>

                        <label class="block">
                            <input type="radio" name="voto" value="ressalva" id="voto_ressalva"
                                class="hidden peer voto-option">

                            <div
                                class="border border-slate-300 rounded-lg p-3 cursor-pointer peer-checked:bg-yellow-600 peer-checked:text-white">
                                A FAVOR, MAS COM RESSALVAS
                            </div>
                        </label>

                        <div id="campoRessalva" class="hidden mt-4">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Descreva suas ressalvas
                            </label>

                            <textarea name="justificativa" rows="4" placeholder="Informe as ressalvas para seu voto..."
                                class="w-full border border-slate-300 rounded-lg p-3 outline-none focus:ring-2 focus:ring-yellow-500 resize-none"></textarea>
                        </div>

                    </div>

                </div>

                <div class="flex justify-center mt-8">

                    <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white font-bold text-xl px-16 py-3 rounded-lg shadow">
                        CONFIRMAR VOTO
                    </button>

                </div>

            </form>

        </div>

    </div>
    <script>
        const radios = document.querySelectorAll('.voto-option');
        const campoRessalva = document.getElementById('campoRessalva');

        radios.forEach(radio => {
            radio.addEventListener('change', () => {
                if (radio.value === 'ressalva' && radio.checked) {
                    campoRessalva.classList.remove('hidden');
                } else {
                    campoRessalva.classList.add('hidden');
                }
            });
        });
    </script>
@endsection
