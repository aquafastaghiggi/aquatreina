<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Aula;
use App\Models\Usuario;

class AulaPolicy
{
    public function before(Usuario $usuario): ?bool
    {
        return $usuario->hasRole('admin') ? true : null;
    }

    public function view(Usuario $usuario, Aula $aula): bool
    {
        return $usuario->can('view', $aula->modulo->curso);
    }

    public function update(Usuario $usuario, Aula $aula): bool
    {
        return $usuario->can('update', $aula->modulo->curso);
    }

    public function delete(Usuario $usuario, Aula $aula): bool
    {
        return $usuario->can('update', $aula->modulo->curso);
    }
}
