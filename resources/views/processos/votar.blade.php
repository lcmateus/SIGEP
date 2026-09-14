@extends('layouts.main_layout', [
    'titulo' => 'Votacao',
])

@section('content')
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-10">
        <h2 class="text-3xl font-bold text-emerald-900">{{ $processo->titulo }}</h2>
        <p class="text-slate-600 font-semibold mb-8">{{ $processo->descricao }}</p>

        <h3 class="text-3xl text-center font-bold text-emerald-900 mb-4">VOTAÇÃO</h3>

        <form method="POST" action="{{ route('processos.votar.store', $processo) }}">
            @csrf

            <div class="grid grid-cols-2 gap-6">
                <div class="border border-slate-300 rounded-lg p-6">
                    <h4 class="text-2xl font-bold text-emerald-900">Registre seu voto</h4>
                    <p class="text-slate-500 font-semibold mt-4">
                        Seu voto só pode ser registrado uma vez. Escolha com atenção.
                    </p>
                </div>

                <div class="space-y-3">
                    <label class="block">
                        <input type="radio" name="tipo" value="favor" class="hidden peer voto-option">
                        <div class="border border-slate-300 rounded-lg p-3 cursor-pointer peer-checked:bg-green-600 peer-checked:text-white">SIM, SOU A FAVOR</div>
                    </label>

                    <label class="block">
                        <input type="radio" name="tipo" value="contra" class="hidden peer voto-option">
                        <div class="border border-slate-300 rounded-lg p-3 cursor-pointer peer-checked:bg-red-600 peer-checked:text-white">NAO, SOU CONTRA</div>
                    </label>

                    <label class="block">
                        <input type="radio" name="tipo" value="abstencao" class="hidden peer voto-option">
                        <div class="border border-slate-300 rounded-lg p-3 cursor-pointer peer-checked:bg-slate-600 peer-checked:text-white">PREFIRO ME ABSTER</div>
                    </label>

                    <label class="block">
                        <input type="radio" name="tipo" value="favor_com_ressalvas" class="hidden peer voto-option">
                        <div class="border border-slate-300 rounded-lg p-3 cursor-pointer peer-checked:bg-yellow-600 peer-checked:text-white">A FAVOR, MAS COM RESSALVAS</div>
                    </label>

                    <div id="campoRessalva" class="hidden mt-4">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Descreva suas ressalvas</label>
                        <textarea name="justificativa" rows="4" placeholder="Informe as ressalvas para seu voto..."
                            class="w-full border border-slate-300 rounded-lg p-3 outline-none focus:ring-2 focus:ring-yellow-500 resize-none">{{ old('justificativa') }}</textarea>
                    </div>

                    @error('tipo') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    @error('justificativa') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex justify-center mt-8">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold text-xl px-16 py-3 rounded-lg shadow">
                    CONFIRMAR VOTO
                </button>
            </div>
        </form>
    </div>

    <script>
        const radios = document.querySelectorAll('.voto-option');
        const campoRessalva = document.getElementById('campoRessalva');

        radios.forEach((radio) => {
            radio.addEventListener('change', () => {
                campoRessalva.classList.toggle('hidden', radio.value !== 'favor_com_ressalvas' || !radio.checked);
            });
        });
    </script>
@endsection
