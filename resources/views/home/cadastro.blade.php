<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - SIGEP</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-100 text-slate-800">
    <div class="min-h-screen flex items-center justify-center">
        <form method="POST" action="{{ route('register') }}" class="bg-white p-6 rounded-2xl shadow w-96 space-y-3">
            @csrf
            <h2 class="text-xl font-bold">Cadastro de Membro</h2>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">SIAPE</label>
                <input type="text" name="siape" class="w-full border border-slate-300 p-2 rounded outline-none focus:ring-2 focus:ring-green-600" placeholder="Digite seu SIAPE" value="{{ old('siape') }}" required>
                @error('siape') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Nome completo</label>
                <input type="text" name="name" class="w-full border border-slate-300 p-2 rounded outline-none focus:ring-2 focus:ring-green-600" placeholder="Digite seu nome completo" value="{{ old('name') }}" required>
                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Email institucional</label>
                <input type="email" name="email" class="w-full border border-slate-300 p-2 rounded outline-none focus:ring-2 focus:ring-green-600" placeholder="Digite seu email" value="{{ old('email') }}" required>
                @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Tipo de membro</label>
                <select name="tipo_membro" class="w-full border border-slate-300 p-2 rounded outline-none focus:ring-2 focus:ring-green-600" required>
                    <option value="">Selecione...</option>
                    <option value="titular" {{ old('tipo_membro') === 'titular' ? 'selected' : '' }}>Titular</option>
                    <option value="suplente" {{ old('tipo_membro') === 'suplente' ? 'selected' : '' }}>Suplente</option>
                </select>
                @error('tipo_membro') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Senha</label>
                <input type="password" name="password" class="w-full border border-slate-300 p-2 rounded outline-none focus:ring-2 focus:ring-green-600" placeholder="Digite uma senha forte" required>
                @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Confirmar senha</label>
                <input type="password" name="password_confirmation" class="w-full border border-slate-300 p-2 rounded outline-none focus:ring-2 focus:ring-green-600" placeholder="Confirme sua senha" required>
            </div>

            <button type="submit" class="w-full bg-green-600 text-white p-2 rounded font-bold hover:bg-green-700 transition-colors">CADASTRAR</button>

            <p class="text-center text-sm">Já tem uma conta? <a href="{{ route('home') }}" class="text-green-700 font-bold hover:underline">Faça login</a></p>
        </form>
    </div>
</body>

</html>
