<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\SituacaoCurso;
use App\Models\Curso;
use App\Models\Matricula;
use App\Models\Usuario;

class MatriculaPolicy
{
    public function before(Usuario $usuario): ?bool
    {
        return $usuario->hasRole('admin') ? true : null;
    }

    public function view(Usuario $usuario, Matricula $matricula): bool
    {
        return $matricula->usuario_id === $usuario->id
            || ($usuario->hasRole('instrutor') && $matricula->curso->responsavel_id === $usuario->id);
    }

    public function create(Usuario $usuario, Curso $curso): bool
    {
        return $curso->situacao === SituacaoCurso::Publicado;
    }

    public function update(Usuario $usuario, Matricula $matricula): bool
    {
        return false;
    }
}
