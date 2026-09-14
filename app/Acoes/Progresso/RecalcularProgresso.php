<?php

declare(strict_types=1);

namespace App\Acoes\Progresso;

use App\Enums\SituacaoAula;
use App\Models\Aula;
use App\Models\Matricula;

final class RecalcularProgresso
{
    public function executar(Matricula $matricula, ?Aula $ultimaAula = null): Matricula
    {
        $aulasPublicadas = Aula::query()
            ->whereHas('modulo', fn ($consulta) => $consulta->where('curso_id', $matricula->curso_id))
            ->where('situacao', SituacaoAula::Publicada)
            ->pluck('id');
        $concluidas = $matricula->progressos()
            ->whereIn('aula_id', $aulasPublicadas)
            ->whereNotNull('concluido_em')
            ->count();
        $percentual = $aulasPublicadas->isEmpty()
            ? 0
            : (int) round(($concluidas / $aulasPublicadas->count()) * 100);
        $dados = ['percentual_progresso' => $percentual];

        if ($ultimaAula !== null) {
            $dados['ultima_aula_id'] = $ultimaAula->id;
        }

        $matricula->update($dados);

        return $matricula->refresh();
    }
}
