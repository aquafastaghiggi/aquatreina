<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\SituacaoComentario;
use App\Enums\SituacaoMatricula;
use App\Models\Aula;
use App\Models\Comentario;
use App\Models\Usuario;

class ComentarioPolicy
{
    public function before(Usuario $usuario, string $habilidade): ?bool
    {
        return $usuario->hasRole('admin') && $habilidade !== 'create' ? true : null;
    }

    public function viewAny(Usuario $usuario): bool
    {
        return $usuario->hasRole('instrutor');
    }

    public function view(Usuario $usuario, Comentario $comentario): bool
    {
        if ($this->gerenciaAula($usuario, $comentario->aula)) {
            return true;
        }

        $matriculado = $usuario->matriculas()
            ->where('curso_id', $comentario->aula->modulo->curso_id)
            ->whereIn('situacao', [SituacaoMatricula::Ativa, SituacaoMatricula::Concluida])
            ->exists();

        return $matriculado && ($comentario->situacao === SituacaoComentario::Aprovado
            || ($comentario->situacao === SituacaoComentario::Pendente
                && $comentario->usuario_id === $usuario->id));
    }

    public function create(Usuario $usuario, Aula $aula): bool
    {
        return $usuario->matriculas()
            ->where('curso_id', $aula->modulo->curso_id)
            ->where('situacao', SituacaoMatricula::Ativa)
            ->exists();
    }

    public function update(Usuario $usuario, Comentario $comentario): bool
    {
        return $this->gerenciaAula($usuario, $comentario->aula);
    }

    public function respond(Usuario $usuario, Comentario $comentario): bool
    {
        return ! $comentario->e_resposta && $this->gerenciaAula($usuario, $comentario->aula);
    }

    public function moderate(Usuario $usuario, Comentario $comentario): bool
    {
        return $this->gerenciaAula($usuario, $comentario->aula);
    }

    private function gerenciaAula(Usuario $usuario, Aula $aula): bool
    {
        return $usuario->hasRole('instrutor')
            && $aula->modulo->curso->responsavel_id === $usuario->id;
    }
}
