<?php

use App\Http\Middleware\DecryptRouteIds;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\PostTooLargeException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(prepend: [
            DecryptRouteIds::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (PostTooLargeException $e, $request) {
            $mensagem = 'O arquivo enviado excede o limite permitido pelo servidor.';

            if ($request->expectsJson()) {
                return response()->json(['ok' => false, 'erro' => $mensagem], 413);
            }

            return back()->withInput()->withErrors(['arquivo' => $mensagem]);
        });
    })->create();
