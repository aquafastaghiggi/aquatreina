<?php

declare(strict_types=1);

namespace App\Servicos\Video;

final class YoutubeProvedor implements ProvedorVideo
{
    public function __construct(
        private readonly ExtratorIdVideo $extrator,
        private readonly LeitorMetadadosVideo $leitor,
        private readonly string $urlAplicacao,
    ) {}

    public function extrairId(string $url): ?string
    {
        return $this->extrator->extrair($url);
    }

    public function metadados(string $videoId): ?MetadadosVideo
    {
        return $this->leitor->ler($videoId);
    }

    public function urlEmbed(string $videoId): string
    {
        $parametros = http_build_query([
            'rel' => 0,
            'modestbranding' => 1,
            'playsinline' => 1,
            'enablejsapi' => 1,
            'controls' => 0,
            'origin' => rtrim($this->urlAplicacao, '/'),
            'cc_lang_pref' => 'pt',
        ]);

        return "https://www.youtube-nocookie.com/embed/{$videoId}?{$parametros}";
    }

    public function urlThumb(string $videoId): string
    {
        return "https://i.ytimg.com/vi/{$videoId}/maxresdefault.jpg";
    }

    public function urlThumbFallback(string $videoId): string
    {
        return "https://i.ytimg.com/vi/{$videoId}/hqdefault.jpg";
    }
}
