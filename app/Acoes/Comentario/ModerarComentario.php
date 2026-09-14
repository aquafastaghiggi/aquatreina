<?php

declare(strict_types=1);

namespace App\Acoes\Comentario;

use App\Enums\SituacaoComentario;
use App\Models\Comentario;
use App\Models\Usuario;
use Illuminate\Auth\Access\AuthorizationException;

final class ModerarComentario
{
    public function executar(
        Usuario $moderador,
        Comentario $comentario,
        ?SituacaoComentario $situacao = null,
        ?bool $fixado = null,
    ): Comentario {
        $comentario->loadMissing('aula.modulo.curso');
        $curso = $comentario->aula->modulo->curso;

        if (! $moderador->hasRole('admin')
            && (! $moderador->hasRole('instrutor') || $curso->responsavel_id !== $moderador->id)) {
            throw new AuthorizationException('Você não pode moderar comentários deste curso.');
        }

        if ($situacao !== null) {
            $comentario->situacao = $situacao;
        }

        if ($fixado !== null) {
            $comentario->fixado = $fixado;
        }

        $comentario->save();

        activity()
            ->causedBy($moderador)
            ->performedOn($comentario)
            ->withProperties(['situacao' => $comentario->situacao->value, 'fixado' => $comentario->fixado])
            ->log('Comentário moderado');

        return $comentario;
    }
}
