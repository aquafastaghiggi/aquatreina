<?php

declare(strict_types=1);

namespace App\Acoes\Curso;

use App\Enums\SituacaoAula;
use App\Models\Curso;

final class RecalcularCachesDoCurso
{
    public function executar(Curso $curso): Curso
    {
        $consulta = $curso->modulos()->with(['aulas' => fn ($query) => $query->where('situacao', SituacaoAula::Publicada)])->get();
        $aulas = $consulta->flatMap->aulas;

        $curso->update([
            'total_aulas' => $aulas->count(),
            'minutos_estimados' => (int) ceil($aulas->sum('duracao_segundos') / 60),
        ]);

        return $curso->refresh();
    }
}
