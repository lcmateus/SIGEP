@extends('layouts/main_layout', [
    'title' => 'Perfil - SIGEP',
    'titulo' => 'PERFIL',
    'usuario' => 'Usuario'
])
@section('content')
@if(session('status'))
    <div class="mb-6 max-w-5xl mx-auto bg-emerald-50 border border-emerald-300 text-emerald-700 rounded-lg px-4 py-3 text-sm font-bold">
        {{ session('status') }}
    </div>
@endif
@if($errors->any())
    <div class="mb-6 max-w-5xl mx-auto bg-red-50 border border-red-300 text-red-600 rounded-lg px-4 py-3 text-sm font-bold">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<div class="bg-white border-2 border-emerald-600 rounded-2xl shadow-sm p-10 max-w-5xl mx-auto">
    <form action="{{ route('perfil.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-3 gap-8">
            <div class="col-span-2">
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-emerald-700 uppercase mb-2">
                            Nome Completo
                        </label>
                        <input
                            type="text"
                            name="nome"
                            value="{{ old('nome', $usuario->nome) }}"
                            class="w-full border border-slate-300 rounded-lg p-4 outline-none focus:ring-2 focus:ring-emerald-500"
                        >
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-emerald-700 uppercase mb-2">
                            E-mail Institucional
                        </label>
                        <input
                            type="email"
                            name="email"
                            value="{{ old('email', $usuario->email) }}"
                            class="w-full border border-slate-300 rounded-lg p-4 outline-none focus:ring-2 focus:ring-emerald-500"
                        >
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-emerald-700 uppercase mb-2">
                            SIAPE
                        </label>
                        <input
                            type="text"
                            value="{{ $usuario->siape }}"
                            readonly
                            title="O SIAPE é a matrícula e não pode ser alterado."
                            class="w-full border border-slate-300 rounded-lg p-4 outline-none bg-slate-100 text-slate-500 cursor-not-allowed"
                        >
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-8 pt-8 border-t border-slate-200">
            <h4 class="text-sm font-bold text-emerald-700 uppercase mb-4">Alterar senha (opcional)</h4>
            <div class="grid grid-cols-2 gap-8">
                <div>
                    <label class="block text-sm font-bold text-emerald-700 uppercase mb-2">
                        Nova senha
                    </label>
                    <input
                        type="password"
                        name="password"
                        class="w-full border border-slate-300 rounded-lg p-4 outline-none focus:ring-2 focus:ring-emerald-500"
                    >
                </div>
                <div>
                    <label class="block text-sm font-bold text-emerald-700 uppercase mb-2">
                        Confirmar nova senha
                    </label>
                    <input
                        type="password"
                        name="password_confirmation"
                        class="w-full border border-slate-300 rounded-lg p-4 outline-none focus:ring-2 focus:ring-emerald-500"
                    >
                </div>
            </div>
        </div>
        <div class="flex justify-end mt-10 pt-8 border-t border-slate-200">
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