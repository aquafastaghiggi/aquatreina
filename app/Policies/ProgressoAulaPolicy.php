<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\SituacaoAula;
use App\Enums\SituacaoMatricula;
use App\Models\Aula;
use App\Models\Usuario;

class ProgressoAulaPolicy
{
    public function create(Usuario $usuario, Aula $aula): bool
    {
        return $aula->situacao === SituacaoAula::Publicada
            && $usuario->matriculas()
                ->where('curso_id', $aula->modulo->curso_id)
                ->where('situacao', SituacaoMatricula::Ativa)
                ->exists();
    }
}
