<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Acoes\Progresso\RecalcularProgresso;
use App\Acoes\Progresso\VerificarConclusaoDoCurso;
use App\Models\Matricula;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

final class ConferirProgressoDiario implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    /** @var list<int> */
    public array $backoff = [60, 300, 900];

    public function handle(RecalcularProgresso $recalcular, VerificarConclusaoDoCurso $verificar): void
    {
        Matricula::query()->chunkById(200, function ($matriculas) use ($recalcular, $verificar): void {
            foreach ($matriculas as $matricula) {
                $recalcular->executar($matricula);
                $verificar->executar($matricula);
            }
        });
    }
}
