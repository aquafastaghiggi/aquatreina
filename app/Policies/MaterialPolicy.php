<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\SituacaoMatricula;
use App\Models\Material;
use App\Models\Usuario;

class MaterialPolicy
{
    public function before(Usuario $usuario, string $habilidade): ?bool
    {
        return $usuario->hasRole('admin') && $habilidade !== 'download' ? true : null;
    }

    public function view(Usuario $usuario, Material $material): bool
    {
        return $this->download($usuario, $material)
            || $usuario->can('view', $material->aula->modulo->curso);
    }

    public function download(Usuario $usuario, Material $material): bool
    {
        $cursoId = $material->aula->modulo->curso_id;

        return $usuario->matriculas()
            ->where('curso_id', $cursoId)
            ->where('situacao', SituacaoMatricula::Ativa)
            ->exists();
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
