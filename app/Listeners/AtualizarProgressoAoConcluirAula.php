<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Acoes\Progresso\RecalcularProgresso;
use App\Eventos\AulaConcluida;

final class AtualizarProgressoAoConcluirAula
{
    public function __construct(private readonly RecalcularProgresso $recalcular) {}

    public function handle(AulaConcluida $evento): void
    {
        $progresso = $evento->progresso->loadMissing(['matricula', 'aula']);
        $this->recalcular->executar($progresso->matricula, $progresso->aula);
    }
}
