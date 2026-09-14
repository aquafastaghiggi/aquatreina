<?php

declare(strict_types=1);

namespace App\Acoes\Curso;

use App\Models\Aula;
use App\Models\Curso;
use App\Models\Modulo;
use Illuminate\Database\DatabaseManager;
use Illuminate\Validation\ValidationException;

final class ReordenarCurriculo
{
    public function __construct(private readonly DatabaseManager $banco) {}

    /** @param list<array{id: int, aulas: list<int>}> $ordem */
    public function executar(Curso $curso, array $ordem): void
    {
        $this->banco->transaction(function () use ($curso, $ordem): void {
            foreach ($ordem as $posicaoModulo => $item) {
                $modulo = Modulo::query()
                    ->whereBelongsTo($curso)
                    ->find($item['id']);

                if ($modulo === null) {
                    throw ValidationException::withMessages(['ordem' => 'Módulo inválido para este curso.']);
                }

                $modulo->update(['posicao' => $posicaoModulo]);

                foreach ($item['aulas'] as $posicaoAula => $aulaId) {
                    $aula = Aula::query()
                        ->whereHas('modulo', fn ($query) => $query->where('curso_id', $curso->id))
                        ->find($aulaId);

                    if ($aula === null) {
                        throw ValidationException::withMessages(['ordem' => 'Aula inválida para este módulo.']);
                    }

                    $aula->update(['modulo_id' => $modulo->id, 'posicao' => $posicaoAula]);
                }
            }
        });
    }
}
