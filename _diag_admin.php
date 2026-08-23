<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

App\Models\UsuarioAdministrador::query()->updateOrCreate(
    ['siape' => '9999998'],
    [
        'nome' => 'Admin Teste',
        'email' => 'adminteste999@ifpi.edu.br',
        'password' => password_hash('senhaadmin123', PASSWORD_BCRYPT),
    ]
);

echo 'temp admin ok';
