<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\SituacaoAula;
use App\Enums\SituacaoMatricula;
use App\Models\Aula;
use App\Models\Curso;
use Illuminate\Http\RedirectResponse;

class CursoController extends Controller
{
    public function entrar(Curso $curso): RedirectResponse
    {
        $matricula = $curso->matriculas()
            ->where('usuario_id', auth()->id())
            ->whereIn('situacao', [SituacaoMatricula::Ativa->value, SituacaoMatricula::Concluida->value])
            ->firstOrFail();
        $aulasConcluidas = $matricula->progressos()->whereNotNull('concluido_em')->pluck('aula_id');
        $aulas = Aula::query()
            ->select('aulas.*')
            ->join('modulos', 'modulos.id', '=', 'aulas.modulo_id')
            ->where('modulos.curso_id', $curso->id)
            ->where('aulas.situacao', SituacaoAula::Publicada)
            ->orderBy('modulos.posicao')
            ->orderBy('aulas.posicao')
            ->get();
        $aula = $aulas->first(fn (Aula $item): bool => ! $aulasConcluidas->contains($item->id)) ?? $aulas->firstOrFail();

        return to_route('app.aula', [$curso, $aula]);
    }
}
