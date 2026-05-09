<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - SIGEP</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-100 text-slate-800"></body>

<div class="min-h-screen flex items-center justify-center">
    <form class="bg-white p-6 rounded-2xl shadow space-y-3">
        <h2 class="text-xl font-bold">Cadastro</h2>
        <input class="w-full border p-2 rounded" placeholder="SIAPE">
        <input class="w-full border p-2 rounded" placeholder="Nome completo">
        <input class="w-full border p-2 rounded" placeholder="Email institucional">
        <select class="w-full border p-2 rounded">
            <option>Titular</option>
            <option>Suplente</option>
        </select>
        <input type="password" class="w-full border p-2 rounded" placeholder="Senha">
        <button class="w-full bg-green-600 text-white p-2 rounded">CADASTRAR</button>
    </form>
</div>
