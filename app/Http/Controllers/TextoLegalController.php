<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\TextoLegal;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class TextoLegalController
{
    public function termos(): View
    {
        return $this->mostrar('termos');
    }

    public function privacidade(): View
    {
        return $this->mostrar('privacidade');
    }

    public function mostrar(string $tipo): View
    {
        abort_unless(in_array($tipo, ['termos', 'privacidade'], true), 404);

        return view('publico.texto-legal', ['tipo' => $tipo, 'texto' => TextoLegal::vigente($tipo)]);
    }

    public function aceite(): View
    {
        return view('conta.aceitar-termos', ['texto' => TextoLegal::vigente('termos')]);
    }

    public function aceitar(Request $request): RedirectResponse
    {
        $request->validate(['aceite_termos' => ['accepted']]);
        $texto = TextoLegal::vigente('termos');
        $request->user()->update([
            'termos_aceitos_em' => now(), 'termos_versao_aceita' => $texto?->versao, 'termos_ip' => $request->ip(),
        ]);

        return redirect()->route('app.painel');
    }
}
