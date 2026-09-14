<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Acoes\Matricula\MatricularAluno;
use App\Enums\OrigemMatricula;
use App\Enums\SituacaoCurso;
use App\Http\Requests\InscreverCursoRequest;
use App\Models\Curso;
use Illuminate\Http\RedirectResponse;

class MatriculaController extends Controller
{
    public function inscrever(
        InscreverCursoRequest $request,
        Curso $curso,
        MatricularAluno $matricular,
    ): RedirectResponse {
        abort_unless($curso->situacao === SituacaoCurso::Publicado, 404);
        $matricular->executar($request->user(), $curso, OrigemMatricula::Aluno);

        return to_route('app.curso', $curso)->with('sucesso', 'Pronto. Bons estudos.');
    }
}
