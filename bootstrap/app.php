<?php

declare(strict_types=1);

use App\Http\Middleware\GarantirUsuarioAtivo;
use App\Http\Middleware\RegistrarUltimoAcesso;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'garantir.ativo' => GarantirUsuarioAtivo::class,
            'registrar.acesso' => RegistrarUltimoAcesso::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
