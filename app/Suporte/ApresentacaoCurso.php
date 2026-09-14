<?php

declare(strict_types=1);

namespace App\Suporte;

use App\Enums\SituacaoAula;
use App\Models\Curso;
use App\Servicos\Video\FabricaProvedorVideo;
use Illuminate\Filesystem\FilesystemManager;

final class ApresentacaoCurso
{
    public function __construct(
        private readonly FabricaProvedorVideo $provedores,
        private readonly FilesystemManager $arquivos,
    ) {}

    public function urlCapa(Curso $curso): ?string
    {
        $capaDemo = public_path("images/demo/{$curso->slug}.svg");

        if (is_file($capaDemo)) {
            return asset("images/demo/{$curso->slug}.svg");
        }

        if (filled($curso->capa_caminho)) {
            return $this->arquivos->disk('public')->url($curso->capa_caminho);
        }

        $aula = $curso->modulos
            ->flatMap->aulas
            ->first(fn ($item): bool => $item->situacao === SituacaoAula::Publicada && filled($item->video_id));

        if ($aula === null) {
            return null;
        }

        return $this->provedores->criar($aula->provedor)->urlThumb($aula->video_id);
    }
}
