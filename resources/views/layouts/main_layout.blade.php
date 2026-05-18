<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('titulo', 'SIGEP - Sistema de Gestão da Ética Pública')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-100 font-sans">
    <div class="flex min-h-screen bg-slate-100">

        @include('partials.asidebar', ['usuario' => $usuario])

        <main class="flex-1 bg-gray-200">

            @include('partials.header', [
                'titulo' => $titulo,
                'usuario' => $usuario,
            ])

            <div class="p-8">
                @yield('content')
            </div>

        </main>

    </div>

</body>

</html>
