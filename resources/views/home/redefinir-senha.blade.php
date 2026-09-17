<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIGEP - Redefinir senha</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-100 text-slate-800">
    <div class="min-h-screen flex items-center justify-center bg-cover bg-center bg-no-repeat"
        style="background-image: url('{{ asset('images/bg-login.jpg') }}');"
    >
        <form method="POST" action="{{ route('password.reset.store') }}" class="bg-white p-8 rounded-2xl shadow w-96 space-y-4">
            @csrf
            <h1 class="text-2xl font-bold text-center">SIGEP</h1>
            <h2 class="text-center text-slate-500 text-sm">REDEFINIR SENHA</h2>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Nova senha</label>
                <div class="relative">
                    <input type="password" name="password" id="password"
                        class="w-full border border-slate-300 p-2 pr-10 rounded outline-none focus:ring-2 focus:ring-green-700"
                        placeholder="Digite a nova senha" required>
                    <button type="button" data-toggle-password="password" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-500 hover:text-slate-700" aria-label="Mostrar ou esconder senha">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </button>
                </div>
                <p class="text-xs text-slate-500 mt-1">A senha deve ter pelo menos 12 caracteres, com pelo menos 1 letra maiúscula, 1 número e 1 caractere especial.</p>
                @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Confirmar nova senha</label>
                <input type="password" name="password_confirmation"
                    class="w-full border border-slate-300 p-2 rounded outline-none focus:ring-2 focus:ring-green-700"
                    placeholder="Repita a nova senha" required>
            </div>

            <button type="submit" class="w-full bg-green-700 text-white p-2 rounded font-bold hover:bg-green-800 transition-colors">SALVAR NOVA SENHA</button>

            <p class="text-center text-sm"><a href="{{ route('home') }}" class="text-green-700 font-bold hover:underline">Voltar para login</a></p>
        </form>
    </div>

    <script>
        document.querySelectorAll('[data-toggle-password]').forEach(function (button) {
            button.addEventListener('click', function () {
                var input = document.getElementById(button.getAttribute('data-toggle-password'));
                input.type = input.type === 'password' ? 'text' : 'password';
            });
        });
    </script>

</body>

</html>