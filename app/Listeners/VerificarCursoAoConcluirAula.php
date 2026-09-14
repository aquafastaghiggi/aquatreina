<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Acoes\Progresso\VerificarConclusaoDoCurso;
use App\Eventos\AulaConcluida;

final class VerificarCursoAoConcluirAula
{
    public function __construct(private readonly VerificarConclusaoDoCurso $verificar) {}

    public function handle(AulaConcluida $evento): void
    {
        $this->verificar->executar($evento->progresso->matricula);
    }
}
