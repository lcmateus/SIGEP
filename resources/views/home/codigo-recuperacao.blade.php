<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIGEP - Código de verificação</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-100 text-slate-800">
    <div class="min-h-screen flex items-center justify-center bg-cover bg-center bg-no-repeat"
        style="background-image: url('{{ asset('images/bg-login.jpg') }}');"
    >
        <form method="POST" action="{{ route('password.code.verify') }}" class="bg-white p-8 rounded-2xl shadow w-96 space-y-4">
            @csrf
            <h1 class="text-2xl font-bold text-center">SIGEP</h1>
            <h2 class="text-center text-slate-500 text-sm">CÓDIGO DE VERIFICAÇÃO</h2>

            @if (session('status'))
                <p class="text-sm text-green-700 text-center">{{ session('status') }}</p>
            @endif

            <p class="text-sm text-slate-500">
                Enviamos um código de 6 caracteres para <strong>{{ $email }}</strong>.
            </p>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Código</label>
                <input type="text" name="codigo" value="{{ old('codigo') }}"
                    class="w-full border border-slate-300 p-2 text-center tracking-widest text-lg uppercase rounded outline-none focus:ring-2 focus:ring-green-700"
                    placeholder="••••••" required minlength="6" maxlength="6">
                @error('codigo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="w-full bg-green-700 text-white p-2 rounded font-bold hover:bg-green-800 transition-colors">VERIFICAR CÓDIGO</button>

            <p class="text-center text-sm">
                Não recebeu? <a href="{{ route('forgot-password') }}" class="text-green-700 font-bold hover:underline">Solicitar novo código</a>
            </p>
            <p class="text-center text-sm"><a href="{{ route('home') }}" class="text-green-700 font-bold hover:underline">Voltar para login</a></p>
        </form>
    </div>
</body>

</html>