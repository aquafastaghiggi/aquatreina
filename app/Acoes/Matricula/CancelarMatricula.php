<?php

declare(strict_types=1);

namespace App\Acoes\Matricula;

use App\Enums\SituacaoMatricula;
use App\Models\Matricula;
use App\Models\Usuario;

final class CancelarMatricula
{
    public function executar(Matricula $matricula, ?Usuario $responsavel = null): Matricula
    {
        $matricula->update(['situacao' => SituacaoMatricula::Cancelada]);

        activity('matriculas')
            ->performedOn($matricula)
            ->causedBy($responsavel)
            ->log('matricula_cancelada');

        return $matricula->refresh();
    }
}
