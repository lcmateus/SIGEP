@extends('layouts/main_layout', [
    'title' => 'Perfil - SIGEP',
    'titulo' => 'PERFIL',
    'usuario' => 'Usuario'
])
@section('content')
<div class="bg-white border-2 border-emerald-600 rounded-2xl shadow-sm p-10 max-w-5xl mx-auto">
    <form action="#" method="POST" enctype="multipart/form-data">
        <div class="grid grid-cols-3 gap-8">
            <div class="col-span-2">
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-emerald-700 uppercase mb-2">
                            Nome Completo
                        </label>
                        <input
                            type="text"
                            class="w-full border border-slate-300 rounded-lg p-4 outline-none focus:ring-2 focus:ring-emerald-500"
                        >
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-emerald-700 uppercase mb-2">
                            Email Institucional
                        </label>
                        <input
                            type="email"
                            class="w-full border border-slate-300 rounded-lg p-4 outline-none focus:ring-2 focus:ring-emerald-500"
                        >
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-emerald-700 uppercase mb-2">
                            SIAPE
                        </label>
                        <input
                            type="text"
                            class="w-full border border-slate-300 rounded-lg p-4 outline-none focus:ring-2 focus:ring-emerald-500"
                        >
                    </div>
                </div>
            </div>
        </div>
        <div class="flex justify-end gap-4 mt-10 pt-8 border-t border-slate-200">
            <button
                type="button"
                class="border border-emerald-600 text-emerald-700 px-10 py-3 rounded-lg font-bold hover:bg-emerald-50 transition-all"
            >
                Editar
            </button>
            <button
                type="submit"
                class="bg-emerald-600 text-white px-10 py-3 rounded-lg font-bold hover:bg-emerald-700 transition-all shadow-md"
            >
                💾 Salvar alterações
            </button>
        </div>
    </form>
</div>

@endsection