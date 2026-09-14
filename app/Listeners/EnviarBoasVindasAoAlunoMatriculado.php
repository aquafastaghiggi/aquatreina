<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Eventos\AlunoMatriculado;
use App\Notifications\BoasVindasAoCurso;
use Illuminate\Contracts\Queue\ShouldQueue;

final class EnviarBoasVindasAoAlunoMatriculado implements ShouldQueue
{
    public string $queue = 'emails';

    public int $tries = 3;

    /** @var list<int> */
    public array $backoff = [60, 300, 900];

    public function handle(AlunoMatriculado $evento): void
    {
        $matricula = $evento->matricula->loadMissing(['usuario', 'curso']);
        $matricula->usuario->notify(new BoasVindasAoCurso($matricula->curso_id));
    }
}
