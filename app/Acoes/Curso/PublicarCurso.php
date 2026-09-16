<?php

declare(strict_types=1);

namespace App\Acoes\Curso;

use App\Enums\SituacaoAula;
use App\Enums\SituacaoCurso;
use App\Eventos\CursoPublicado;
use App\Excecoes\CursoIncompleto;
use App\Models\Curso;
use App\Models\Usuario;
use Illuminate\Contracts\Cache\Factory as Cache;
use Illuminate\Contracts\Events\Dispatcher;

final class PublicarCurso
{
    public function __construct(
        private readonly RecalcularCachesDoCurso $recalcularCaches,
        private readonly Dispatcher $eventos,
        private readonly Cache $cache,
    ) {}

    public function executar(Curso $curso, ?Usuario $executor = null): Curso
    {
        $curso->load('modulos.aulas');
        $aulasPublicadas = $curso->modulos->flatMap->aulas
            ->where('situacao', SituacaoAula::Publicada);
        $pendencias = [];

        if (blank($curso->titulo)) {
            $pendencias[] = 'Informe o título.';
        }

        if ($curso->categoria_id === null) {
            $pendencias[] = 'Selecione uma categoria.';
        }

        if ($aulasPublicadas->isEmpty()) {
            $pendencias[] = 'Publique ao menos uma aula dentro de um módulo.';
        }

        if (blank($curso->capa_caminho) && $aulasPublicadas->whereNotNull('video_id')->isEmpty()) {
            $pendencias[] = 'Envie uma capa ou informe um vídeo na primeira aula publicada.';
        }

        foreach ($aulasPublicadas->whereNull('video_id') as $aula) {
            $pendencias[] = "A aula '{$aula->titulo}' não possui vídeo.";
        }

        if ($pendencias !== []) {
            throw new CursoIncompleto($pendencias);
        }

        $curso->update([
            'situacao' => SituacaoCurso::Publicado,
            'publicado_em' => now(),
        ]);
        $curso = $this->recalcularCaches->executar($curso);
        $this->cache->store()->forget('catalogo:publicados');
        $this->eventos->dispatch(new CursoPublicado($curso));

        activity()->causedBy($executor)->performedOn($curso)->log('Curso publicado');

        return $curso;
    }
}
