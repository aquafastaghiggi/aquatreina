<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Acoes\Curso\RecalcularCachesDoCurso;
use App\Eventos\AulaPublicada;

final class RecalcularCachesAoPublicarAula
{
    public function __construct(private readonly RecalcularCachesDoCurso $recalcular) {}

    public function handle(AulaPublicada $evento): void
    {
        $this->recalcular->executar($evento->aula->modulo->curso);
    }
}
