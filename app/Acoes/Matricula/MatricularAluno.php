<?php

declare(strict_types=1);

namespace App\Acoes\Matricula;

use App\Enums\OrigemMatricula;
use App\Enums\SituacaoCurso;
use App\Enums\SituacaoMatricula;
use App\Eventos\AlunoMatriculado;
use App\Models\Curso;
use App\Models\Matricula;
use App\Models\Usuario;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final class MatricularAluno
{
    public function __construct(private readonly Dispatcher $eventos) {}

    public function executar(
        Usuario $usuario,
        Curso $curso,
        OrigemMatricula $origem = OrigemMatricula::Aluno,
        ?Usuario $responsavel = null,
    ): Matricula {
        if ($curso->situacao !== SituacaoCurso::Publicado) {
            throw (new ModelNotFoundException)->setModel(Curso::class, [$curso->id]);
        }

        $matricula = Matricula::query()->createOrFirst(
            ['usuario_id' => $usuario->id, 'curso_id' => $curso->id],
            [
                'origem' => $origem,
                'situacao' => SituacaoMatricula::Ativa,
                'matriculado_em' => now(),
            ],
        );
        $reativada = $matricula->situacao === SituacaoMatricula::Cancelada;

        if ($reativada) {
            $matricula->update([
                'origem' => $origem,
                'situacao' => SituacaoMatricula::Ativa,
                'concluido_em' => null,
            ]);
        }

        if ($matricula->wasRecentlyCreated || $reativada) {
            if ($origem === OrigemMatricula::Admin) {
                activity('matriculas')
                    ->performedOn($matricula)
                    ->causedBy($responsavel)
                    ->log($reativada ? 'matricula_reativada' : 'matricula_criada');
            }

            $this->eventos->dispatch(new AlunoMatriculado($matricula));
        }

        return $matricula->refresh();
    }
}
