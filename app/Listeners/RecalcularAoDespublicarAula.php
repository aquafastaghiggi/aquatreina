<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Acoes\Curso\RecalcularCachesDoCurso;
use App\Acoes\Progresso\RecalcularMatriculasDoCurso;
use App\Eventos\AulaDespublicada;

final class RecalcularAoDespublicarAula
{
    public function __construct(
        private readonly RecalcularCachesDoCurso $recalcularCurso,
        private readonly RecalcularMatriculasDoCurso $recalcularMatriculas,
    ) {}

    public function handle(AulaDespublicada $evento): void
    {
        $curso = $evento->aula->modulo->curso;
        $this->recalcularCurso->executar($curso);
        $this->recalcularMatriculas->executar($curso);
    }
}
