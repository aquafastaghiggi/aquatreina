<?php

declare(strict_types=1);

namespace App\Servicos\Video;

interface ProvedorVideo
{
    public function extrairId(string $url): ?string;

    public function metadados(string $videoId): ?MetadadosVideo;

    public function urlEmbed(string $videoId): string;

    public function urlThumb(string $videoId): string;
}
