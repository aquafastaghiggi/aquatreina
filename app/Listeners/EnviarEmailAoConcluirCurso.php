<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Eventos\CursoConcluido;
use App\Notifications\CursoConcluidoComSucesso;
use Illuminate\Contracts\Queue\ShouldQueue;

final class EnviarEmailAoConcluirCurso implements ShouldQueue
{
    public string $queue = 'emails';

    public int $tries = 3;

    /** @var list<int> */
    public array $backoff = [60, 300, 900];

    public function handle(CursoConcluido $evento): void
    {
        $matricula = $evento->matricula->loadMissing(['usuario', 'curso']);
        $matricula->usuario->notify(new CursoConcluidoComSucesso($matricula->curso_id));
    }
}
