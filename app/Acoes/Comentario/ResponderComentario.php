<?php

declare(strict_types=1);

namespace App\Acoes\Comentario;

use App\Enums\SituacaoComentario;
use App\Eventos\ComentarioRespondido;
use App\Models\Comentario;
use App\Models\Usuario;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;

final class ResponderComentario
{
    public function executar(Usuario $autor, Comentario $pergunta, string $corpo): Comentario
    {
        $corpo = trim($corpo);

        if (mb_strlen($corpo) < 3 || mb_strlen($corpo) > 5000) {
            throw ValidationException::withMessages(['corpo' => 'A resposta deve ter entre 3 e 5.000 caracteres.']);
        }

        $pergunta->loadMissing('aula.modulo.curso');
        $curso = $pergunta->aula->modulo->curso;

        if (! $autor->hasRole('admin')
            && (! $autor->hasRole('instrutor') || $curso->responsavel_id !== $autor->id)) {
            throw new AuthorizationException('Você não pode responder perguntas deste curso.');
        }

        if ($pergunta->comentario_pai_id !== null || $pergunta->e_resposta) {
            throw ValidationException::withMessages([
                'corpo' => 'A resposta deve estar vinculada diretamente a uma pergunta.',
            ]);
        }

        $resposta = Comentario::query()->create([
            'aula_id' => $pergunta->aula_id,
            'usuario_id' => $autor->id,
            'comentario_pai_id' => $pergunta->id,
            'corpo' => $corpo,
            'situacao' => SituacaoComentario::Aprovado,
            'e_resposta' => true,
        ]);

        ComentarioRespondido::dispatch($resposta);

        return $resposta;
    }
}
