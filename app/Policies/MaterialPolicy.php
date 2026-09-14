<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Material;
use App\Models\Usuario;

class MaterialPolicy
{
    public function before(Usuario $usuario): ?bool
    {
        return $usuario->hasRole('admin') ? true : null;
    }

    public function view(Usuario $usuario, Material $material): bool
    {
        return $usuario->can('view', $material->aula->modulo->curso);
    }

    public function update(Usuario $usuario, Material $material): bool
    {
        return $usuario->can('update', $material->aula->modulo->curso);
    }

    public function delete(Usuario $usuario, Material $material): bool
    {
        return $usuario->can('update', $material->aula->modulo->curso);
    }
}
