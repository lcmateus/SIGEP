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
            <p class="text-xs text-slate-500 mb-4">A senha deve ter pelo menos 12 caracteres, com pelo menos 1 letra maiúscula, 1 número e 1 caractere especial.</p>
            <div class="grid grid-cols-2 gap-8">
                <div>
                    <label class="block text-sm font-bold text-emerald-700 uppercase mb-2">
                        Nova senha
                    </label>
                    <div class="relative">
                        <input
                            type="password"
                            name="password"
                            id="password"
                            class="w-full border border-slate-300 rounded-lg p-4 pr-12 outline-none focus:ring-2 focus:ring-emerald-500"
                        >
                        <button type="button" data-toggle-password="password" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-500 hover:text-slate-700" aria-label="Mostrar ou esconder senha">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-emerald-700 uppercase mb-2">
                        Confirmar nova senha
                    </label>
                    <div class="relative">
                        <input
                            type="password"
                            name="password_confirmation"
                            id="password_confirmation"
                            class="w-full border border-slate-300 rounded-lg p-4 pr-12 outline-none focus:ring-2 focus:ring-emerald-500"
                        >
                        <button type="button" data-toggle-password="password_confirmation" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-500 hover:text-slate-700" aria-label="Mostrar ou esconder senha">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </button>
                    </div>
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
@push('scripts')
<script>
    document.querySelectorAll('[data-toggle-password]').forEach(function (button) {
        button.addEventListener('click', function () {
            var input = document.getElementById(button.getAttribute('data-toggle-password'));
            input.type = input.type === 'password' ? 'text' : 'password';
        });
    });
</script>
@endpush
@endsection