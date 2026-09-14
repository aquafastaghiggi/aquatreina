<?php

declare(strict_types=1);

namespace App\Servicos\Video;

use App\Enums\ProvedorVideo as TipoProvedorVideo;
use InvalidArgumentException;

final class FabricaProvedorVideo
{
    public function __construct(private readonly YoutubeProvedor $youtube) {}

    public function criar(TipoProvedorVideo $provedor): ProvedorVideo
    {
        return match ($provedor) {
            TipoProvedorVideo::Youtube => $this->youtube,
            default => throw new InvalidArgumentException('Provedor de vídeo ainda não implementado.'),
        };
    }
}
