<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\SituacaoMatricula;
use App\Models\Curso;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;

class CursoController extends Controller
{
    public function entrar(Curso $curso): View
    {
        $matricula = $curso->matriculas()
            ->where('usuario_id', auth()->id())
            ->whereIn('situacao', [SituacaoMatricula::Ativa->value, SituacaoMatricula::Concluida->value])
            ->firstOrFail();
        Gate::authorize('view', $matricula);

        return view('aluno.curso', compact('curso', 'matricula'));
    }
}
