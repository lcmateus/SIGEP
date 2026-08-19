<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/favicon.ico" sizes="32x32">
    <link rel="icon" href="/genfavicon-32.png" sizes="32x32">
    <link rel="icon" href="/genfavicon-16.png" sizes="16x16">
    <link rel="apple-touch-icon" href="/apple-touch-icon-57x57.png" sizes="57x57">
    <link rel="apple-touch-icon" href="/apple-touch-icon-114x114.png" sizes="114x114">
    <link rel="apple-touch-icon" href="/apple-touch-icon-120x120.png" sizes="120x120">
    <link rel="apple-touch-icon" href="/apple-touch-icon-180x180.png" sizes="180x180">
    <link rel="manifest" href="/site.webmanifest">
    <title>@yield('titulo', 'SIGEP - Sistema de Gestão da Ética Pública')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-100 font-sans">
    @php
        $authUser = auth()->user();
        $role = $authUser instanceof \App\Models\UsuarioAdministrador
            ? 'admin'
            : ($authUser instanceof \App\Models\UsuarioMembro ? 'membro' : 'guest');
    @endphp
    <div class="flex min-h-screen bg-slate-100">

        @include('partials.asidebar', ['usuario' => $role])

        <main class="flex-1 bg-gray-200">

            @include('partials.header', [
                'titulo' => $titulo ?? 'Dashboard',
                'usuario' => $authUser?->nome ?? 'Usuário',
            ])

            <div class="p-8">
                @yield('content')
            </div>

        </main>

    </div>
</body>

</html>
