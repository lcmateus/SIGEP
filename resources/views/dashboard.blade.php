<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sucesso - SIGEP</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 font-sans">

    <div class="flex min-h-screen bg-slate-100">
        <?php
            $usuario = 'Admin';
        ?>
        @include('partials.asidebar', ['usuario' => $usuario])
        @include('partials.dashboard_main', ['usuario' => $usuario])
    </div>

</body>
</html>
