<?php

declare(strict_types=1);

namespace App\Servicos\Video;

final readonly class MetadadosVideo
{
    public function __construct(
        public string $titulo,
        public int $duracaoSegundos,
        public ?string $thumb = null,
        public bool $duracaoAutomatica = true,
    ) {}
}
