<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class CabecalhosSeguranca
{
    public function handle(Request $request, Closure $next): Response
    {
        $resposta = $next($request);
        $resposta->headers->set('Content-Security-Policy', implode('; ', [
            "default-src 'self'",
            "base-uri 'self'",
            "object-src 'none'",
            "frame-ancestors 'self'",
            'frame-src https://www.youtube-nocookie.com',
            "script-src 'self' 'unsafe-inline' https://www.youtube.com",
            "style-src 'self' 'unsafe-inline'",
            "img-src 'self' data: https://i.ytimg.com https://img.youtube.com",
            "font-src 'self' data:",
            "connect-src 'self'",
            "form-action 'self'",
        ]));
        $resposta->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $resposta->headers->set('X-Content-Type-Options', 'nosniff');
        $resposta->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $resposta->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        if (app()->isProduction() || config('seguranca.forcar_https')) {
            $resposta->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $resposta;
    }
}
