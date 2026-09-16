<?php

declare(strict_types=1);

namespace App\Acoes\Curso;

use App\Enums\SituacaoCurso;
use App\Models\Curso;
use App\Models\Usuario;
use Illuminate\Contracts\Cache\Factory as Cache;

final class ArquivarCurso
{
    public function __construct(private readonly Cache $cache) {}

    public function executar(Curso $curso, ?Usuario $executor = null): Curso
    {
        $curso->update(['situacao' => SituacaoCurso::Arquivado]);
        $this->cache->store()->forget('catalogo:publicados');

        activity()->causedBy($executor)->performedOn($curso)->log('Curso arquivado');

        return $curso->refresh();
    }
}
