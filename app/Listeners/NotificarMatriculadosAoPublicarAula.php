<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Enums\SituacaoMatricula;
use App\Eventos\AulaPublicada;
use App\Notifications\NovoConteudoNoCurso;
use Illuminate\Contracts\Queue\ShouldQueue;

final class NotificarMatriculadosAoPublicarAula implements ShouldQueue
{
    public string $queue = 'emails';

    public int $tries = 3;

    /** @var list<int> */
    public array $backoff = [60, 300, 900];

    public function handle(AulaPublicada $evento): void
    {
        $curso = $evento->aula->loadMissing('modulo.curso')->modulo->curso;

        $curso->matriculas()
            ->where('situacao', '!=', SituacaoMatricula::Cancelada)
            ->with('usuario')
            ->chunkById(100, function ($matriculas) use ($evento): void {
                foreach ($matriculas as $matricula) {
                    $matricula->usuario->notify(new NovoConteudoNoCurso($evento->aula->id));
                }
            });
    }
}
