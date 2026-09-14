<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Acoes\Progresso\RecalcularMatriculasDoCurso;
use App\Eventos\AulaPublicada;

final class RecalcularMatriculasAoPublicarAula
{
    public function __construct(private readonly RecalcularMatriculasDoCurso $recalcular) {}

    public function handle(AulaPublicada $evento): void
    {
        $this->recalcular->executar($evento->aula->modulo->curso);
    }
}
