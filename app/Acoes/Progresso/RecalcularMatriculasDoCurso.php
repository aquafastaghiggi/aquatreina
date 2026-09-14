<?php

declare(strict_types=1);

namespace App\Acoes\Progresso;

use App\Models\Curso;

final class RecalcularMatriculasDoCurso
{
    public function __construct(
        private readonly RecalcularProgresso $recalcular,
        private readonly VerificarConclusaoDoCurso $verificarConclusao,
    ) {}

    public function executar(Curso $curso): void
    {
        $curso->matriculas()->chunkById(200, function ($matriculas): void {
            foreach ($matriculas as $matricula) {
                $this->recalcular->executar($matricula);
                $this->verificarConclusao->executar($matricula);
            }
        });
    }
}
