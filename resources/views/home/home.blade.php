<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIGEP</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-100 text-slate-800"></body>

<div class="min-h-screen flex items-center justify-center bg-slate-100">
    <form class="bg-white p-8 rounded-2xl shadow w-96 space-y-4">
        <h1 class="text-2xl font-bold text-center">SIGEP</h1>
        <input class="w-full border p-2 rounded" placeholder="SIAPE">
        <input type="password" class="w-full border p-2 rounded" placeholder="Senha">
        <button class="w-full bg-green-700 text-white p-2 rounded">ENTRAR</button>
        <p class="text-center text-sm">Não tem uma conta? <a href="{{ route('cadastro') }}"
                class="text-green-700 font-bold">Cadastre-se</a></p>
    </form>
</div>
