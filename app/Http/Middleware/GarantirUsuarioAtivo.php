<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\SituacaoUsuario;
use App\Models\TextoLegal;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class GarantirUsuarioAtivo
{
    public function handle(Request $request, Closure $next): Response|RedirectResponse
    {
        $usuario = $request->user();

        if ($usuario?->situacao === SituacaoUsuario::Bloqueado) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Sua conta está bloqueada. Fale com a Aquafast.',
            ]);
        }

        if ($usuario?->situacao === SituacaoUsuario::Pendente) {
            return redirect()->route('conta.pendente');
        }

        $termos = TextoLegal::vigente('termos');
        if ($termos !== null && $usuario?->termos_versao_aceita !== $termos->versao) {
            return redirect()->route('termos.aceite');
        }

        return $next($request);
    }
}
