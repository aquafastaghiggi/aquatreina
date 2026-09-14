<?php

declare(strict_types=1);

namespace App\Acoes\Curso;

use App\Enums\SituacaoCurso;
use App\Models\Curso;
use Illuminate\Contracts\Cache\Factory as Cache;

final class ArquivarCurso
{
    public function __construct(private readonly Cache $cache) {}

    public function executar(Curso $curso): Curso
    {
        $curso->update(['situacao' => SituacaoCurso::Arquivado]);
        $this->cache->store()->forget('catalogo:publicados');

        return $curso->refresh();
    }
}
