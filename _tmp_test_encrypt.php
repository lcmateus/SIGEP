<?php

require __DIR__.'/vendor/autoload.php';

$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Processo;
use App\Support\EncryptedId;
use Illuminate\Http\Request;

$http = $app->make(Illuminate\Contracts\Http\Kernel::class);

$p = Processo::query()->first();
$encKey = $p->getRouteKey();
$encCorreto = EncryptedId::encrypt($p->numero_sei);
$encErrado = EncryptedId::encrypt('zzz.nao-existe');

foreach ([
    'criptografado correto' => "/processos/{$encCorreto}",
    'criptografado inexistente' => "/processos/{$encErrado}",
    'texto lixo' => '/processos/valor-invalido',
] as $label => $uri) {
    $resp = $http->handle(Request::create($uri, 'GET'));
    echo sprintf(
        "%-28s status=%d location=%s\n",
        $label,
        $resp->getStatusCode(),
        $resp->headers->get('Location', '-')
    );
}
