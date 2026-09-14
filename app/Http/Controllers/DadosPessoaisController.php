<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class DadosPessoaisController
{
    public function exportar(Request $request): JsonResponse
    {
        $usuario = $request->user()->load([
            'organizacao', 'matriculas.curso', 'matriculas.progressos.aula', 'comentarios.aula', 'notifications',
        ]);

        return response()->json([
            'gerado_em' => now()->toIso8601String(),
            'usuario' => $usuario->makeHidden(['password', 'remember_token'])->toArray(),
        ], headers: ['Content-Disposition' => 'attachment; filename="meus-dados-aquafast-treina.json"']);
    }
}
