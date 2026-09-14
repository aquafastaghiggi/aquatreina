<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class RegistrarUltimoAcesso
{
    public function handle(Request $request, Closure $next): Response
    {
        $usuario = $request->user();

        if ($usuario && (! $usuario->ultimo_acesso_em || $usuario->ultimo_acesso_em->lte(now()->subHour()))) {
            $usuario->forceFill(['ultimo_acesso_em' => now()])->save();
        }

        return $next($request);
    }
}
