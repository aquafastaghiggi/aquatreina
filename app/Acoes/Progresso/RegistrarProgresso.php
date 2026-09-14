<?php

declare(strict_types=1);

namespace App\Acoes\Progresso;

use App\Enums\SituacaoAula;
use App\Enums\SituacaoMatricula;
use App\Models\Aula;
use App\Models\Matricula;
use App\Models\ProgressoAula;
use App\Models\Usuario;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Validation\ValidationException;

final class RegistrarProgresso
{
    public function __construct(
        private readonly ConnectionInterface $banco,
        private readonly ConcluirAula $concluirAula,
    ) {}

    public function executar(Usuario $usuario, Aula $aula, int $posicao): ProgressoAula
    {
        if ($aula->situacao !== SituacaoAula::Publicada) {
            throw new AuthorizationException('Aula não publicada.');
        }

        $matricula = Matricula::query()
            ->where('usuario_id', $usuario->id)
            ->where('curso_id', $aula->modulo->curso_id)
            ->where('situacao', SituacaoMatricula::Ativa)
            ->first();

        if ($matricula === null) {
            throw new AuthorizationException('Matrícula ativa não encontrada.');
        }

        if ($posicao < 0 || $posicao > $aula->duracao_segundos + 5) {
            throw ValidationException::withMessages(['posicao' => 'A posição informada é inválida.']);
        }

        [$progresso, $avancoNormal] = $this->banco->transaction(function () use ($matricula, $aula, $posicao): array {
            ProgressoAula::query()->createOrFirst(
                ['matricula_id' => $matricula->id, 'aula_id' => $aula->id],
                ['primeira_visualizacao_em' => now()],
            );
            $progresso = ProgressoAula::query()
                ->whereBelongsTo($matricula)
                ->whereBelongsTo($aula)
                ->lockForUpdate()
                ->firstOrFail();
            $delta = $posicao - $progresso->posicao_maxima;

            if ($delta <= 0) {
                return [$progresso, false];
            }

            $dados = ['posicao_maxima' => $posicao];
            $tolerancia = (float) config('treina.tolerancia_salto');
            $avancoNormal = $delta <= $tolerancia;

            if ($avancoNormal) {
                $dados['segundos_assistidos'] = $progresso->segundos_assistidos + $delta;
            }

            $progresso->update($dados);

            return [$progresso->refresh(), $avancoNormal];
        });

        $matricula->update(['ultima_aula_id' => $aula->id]);

        if ($avancoNormal) {
            return $this->concluirAula->executar($matricula, $aula);
        }

        return $progresso->refresh();
    }
}
