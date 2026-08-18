<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIGEP - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="text-slate-800">  
   
    <div class="min-h-screen flex items-center justify-center bg-[url('/images/bg-login.jpg')] bg-cover">
        <form method="POST" action="{{ route('login') }}" class="bg-white p-8 rounded-2xl shadow w-96 space-y-4">
            @csrf
            <h1 class="text-2xl font-bold text-center">SIGEP</h1>

            @if (session('status'))
                <p class="text-sm text-green-700 text-center">{{ session('status') }}</p>
            @endif
            
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">SIAPE</label>
                <input type="text" name="siape" class="w-full border border-slate-300 p-2 rounded outline-none focus:ring-2 focus:ring-green-700" placeholder="Digite seu SIAPE" required>
                @error('siape') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Senha</label>
                <input type="password" name="password" class="w-full border border-slate-300 p-2 rounded outline-none focus:ring-2 focus:ring-green-700" placeholder="Digite sua senha" required>
                @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <a class = "text-center text-sm text-green-700 hover:underline" href="{{ route('forgot-password') }}">Esqueci minha senha</a>
            </div>
            
            <button type="submit" class="w-full bg-green-700 text-white p-2 rounded font-bold hover:bg-green-800 transition-colors">ENTRAR</button>
            <p class="text-center text-sm">Não tem uma conta? <a href="{{ route('cadastro') }}" class="text-green-700 font-bold hover:underline">Cadastre-se</a></p>
        </form>
    </div>

</body>

</html>
