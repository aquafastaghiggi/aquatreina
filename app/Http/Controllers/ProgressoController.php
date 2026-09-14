<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Acoes\Progresso\RegistrarProgresso;
use App\Http\Requests\RegistrarProgressoRequest;
use App\Models\Aula;
use App\Models\ProgressoAula;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class ProgressoController extends Controller
{
    public function registrar(RegistrarProgressoRequest $request, RegistrarProgresso $registrar): JsonResponse
    {
        $dados = $request->validated();
        $aula = Aula::query()->with('modulo')->findOrFail($dados['aula_id']);
        Gate::authorize('create', [ProgressoAula::class, $aula]);
        $progresso = $registrar->executar($request->user(), $aula, $dados['posicao']);

        return response()->json([
            'posicao_maxima' => $progresso->posicao_maxima,
            'segundos_assistidos' => $progresso->segundos_assistidos,
            'concluida' => $progresso->concluido_em !== null,
            'percentual_curso' => $progresso->matricula->fresh()->percentual_progresso,
        ]);
    }
}
