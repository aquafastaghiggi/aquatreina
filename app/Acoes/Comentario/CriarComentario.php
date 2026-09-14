<?php

declare(strict_types=1);

namespace App\Acoes\Comentario;

use App\Enums\SituacaoComentario;
use App\Enums\SituacaoMatricula;
use App\Models\Aula;
use App\Models\Comentario;
use App\Models\Usuario;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Cache\RateLimiter;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

final class CriarComentario
{
    public function __construct(private readonly RateLimiter $limitador) {}

    public function executar(Usuario $usuario, Aula $aula, string $corpo): Comentario
    {
        $corpo = trim($corpo);

        if (mb_strlen($corpo) < 3 || mb_strlen($corpo) > 5000) {
            throw ValidationException::withMessages(['corpo' => 'A pergunta deve ter entre 3 e 5.000 caracteres.']);
        }

        $aula->loadMissing('modulo.curso');
        $matriculado = $usuario->matriculas()
            ->where('curso_id', $aula->modulo->curso_id)
            ->where('situacao', SituacaoMatricula::Ativa)
            ->exists();

        if (! $matriculado) {
            throw new AuthorizationException('Você precisa de uma matrícula ativa para perguntar.');
        }

        $chave = "comentarios:usuario:{$usuario->id}";
        $limite = (int) config('treina.limite_comentarios_minuto');

        if ($this->limitador->tooManyAttempts($chave, $limite)) {
            throw new TooManyRequestsHttpException(
                $this->limitador->availableIn($chave),
                'Você atingiu o limite de comentários. Tente novamente em instantes.',
            );
        }

        $this->limitador->hit($chave, 60);

        return Comentario::query()->create([
            'aula_id' => $aula->id,
            'usuario_id' => $usuario->id,
            'corpo' => $corpo,
            'situacao' => $aula->modulo->curso->moderar_comentarios
                ? SituacaoComentario::Pendente
                : SituacaoComentario::Aprovado,
        ]);
    }
}
