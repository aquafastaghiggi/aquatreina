<?php

declare(strict_types=1);

namespace App\Acoes\Progresso;

use App\Enums\SituacaoAula;
use App\Enums\SituacaoMatricula;
use App\Eventos\CursoConcluido;
use App\Models\Aula;
use App\Models\Matricula;
use Illuminate\Contracts\Events\Dispatcher;

final class VerificarConclusaoDoCurso
{
    public function __construct(private readonly Dispatcher $eventos) {}

    public function executar(Matricula $matricula): Matricula
    {
        $aulasPublicadas = Aula::query()
            ->whereHas('modulo', fn ($consulta) => $consulta->where('curso_id', $matricula->curso_id))
            ->where('situacao', SituacaoAula::Publicada)
            ->pluck('id');
        $todasConcluidas = $aulasPublicadas->isNotEmpty()
            && $matricula->progressos()
                ->whereIn('aula_id', $aulasPublicadas)
                ->whereNotNull('concluido_em')
                ->distinct('aula_id')
                ->count('aula_id') === $aulasPublicadas->count();

        if ($todasConcluidas && $matricula->situacao !== SituacaoMatricula::Concluida) {
            $matricula->update([
                'situacao' => SituacaoMatricula::Concluida,
                'concluido_em' => $matricula->concluido_em ?? now(),
            ]);
            $this->eventos->dispatch(new CursoConcluido($matricula->refresh()));

            return $matricula->refresh();
        }

        if (! $todasConcluidas && $matricula->situacao === SituacaoMatricula::Concluida) {
            $matricula->update(['situacao' => SituacaoMatricula::Ativa]);
        }

        return $matricula->refresh();
    }
}
