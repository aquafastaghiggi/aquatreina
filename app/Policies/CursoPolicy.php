<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Curso;
use App\Models\Usuario;

class CursoPolicy
{
    public function before(Usuario $usuario, string $ability): ?bool
    {
        if ($ability === 'delete') {
            return null;
        }

        return $usuario->hasRole('admin') ? true : null;
    }

    public function viewAny(Usuario $usuario): bool
    {
        return $usuario->hasRole('instrutor');
    }

    public function view(Usuario $usuario, Curso $curso): bool
    {
        return $this->pertenceAoInstrutor($usuario, $curso);
    }

    public function create(Usuario $usuario): bool
    {
        return $usuario->hasRole('instrutor');
    }

    public function update(Usuario $usuario, Curso $curso): bool
    {
        return $this->pertenceAoInstrutor($usuario, $curso);
    }

    public function delete(Usuario $usuario, Curso $curso): bool
    {
        if ($curso->matriculas()->exists()) {
            return false;
        }

        return $usuario->hasRole('admin') || $this->pertenceAoInstrutor($usuario, $curso);
    }

    private function pertenceAoInstrutor(Usuario $usuario, Curso $curso): bool
    {
        return $usuario->hasRole('instrutor') && $curso->responsavel_id === $usuario->id;
    }
}
