<?php

declare(strict_types=1);

namespace App\Acoes\Curso;

use App\Enums\SituacaoCurso;
use App\Models\Curso;

final class ArquivarCurso
{
    public function executar(Curso $curso): Curso
    {
        $curso->update(['situacao' => SituacaoCurso::Arquivado]);

        return $curso->refresh();
    }
}
