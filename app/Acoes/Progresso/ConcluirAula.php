<?php

declare(strict_types=1);

namespace App\Acoes\Progresso;

use App\Eventos\AulaConcluida;
use App\Models\Aula;
use App\Models\Configuracao;
use App\Models\Matricula;
use App\Models\ProgressoAula;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\ConnectionInterface;

final class ConcluirAula
{
    public function __construct(
        private readonly ConnectionInterface $banco,
        private readonly Dispatcher $eventos,
    ) {}

    public function executar(Matricula $matricula, Aula $aula, bool $manual = false): ProgressoAula
    {
        [$progresso, $concluiuAgora] = $this->banco->transaction(function () use ($matricula, $aula, $manual): array {
            ProgressoAula::query()->createOrFirst(
                ['matricula_id' => $matricula->id, 'aula_id' => $aula->id],
                ['primeira_visualizacao_em' => now()],
            );
            $progresso = ProgressoAula::query()
                ->whereBelongsTo($matricula)
                ->whereBelongsTo($aula)
                ->lockForUpdate()
                ->firstOrFail();

            if ($progresso->concluido_em !== null) {
                return [$progresso, false];
            }

            if (! $manual && ! $this->atingiuLimiteAutomatico($progresso, $aula)) {
                return [$progresso, false];
            }

            $progresso->update(['concluido_em' => now()]);

            return [$progresso->refresh(), true];
        });

        if ($concluiuAgora) {
            $this->eventos->dispatch(new AulaConcluida($progresso));
        }

        return $progresso->refresh();
    }

    private function atingiuLimiteAutomatico(ProgressoAula $progresso, Aula $aula): bool
    {
        if ($aula->duracao_segundos === 0) {
            return false;
        }

        $limite = $aula->duracao_segundos * ((int) Configuracao::valor('percentual_conclusao', config('treina.percentual_conclusao')) / 100);

        return $progresso->posicao_maxima >= $limite
            && $progresso->segundos_assistidos >= $limite;
    }
}
