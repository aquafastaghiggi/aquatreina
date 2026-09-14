<?php

declare(strict_types=1);

namespace App\Servicos\Video;

use DateInterval;
use Illuminate\Contracts\Cache\Factory as FabricaCache;
use Illuminate\Http\Client\Factory as ClienteHttp;
use Throwable;

final class LeitorMetadadosVideo
{
    private const TEMPO_CACHE_SEGUNDOS = 86400;

    public function __construct(
        private readonly ClienteHttp $http,
        private readonly FabricaCache $cache,
        private readonly ?string $chaveApi,
    ) {}

    public function ler(string $videoId): ?MetadadosVideo
    {
        return $this->cache->store()->remember(
            "video:metadados:youtube:{$videoId}",
            self::TEMPO_CACHE_SEGUNDOS,
            fn (): ?MetadadosVideo => $this->consultar($videoId),
        );
    }

    public function converterDuracao(string $duracao): int
    {
        try {
            $intervalo = new DateInterval($duracao);

            return ($intervalo->d * 86400) + ($intervalo->h * 3600) + ($intervalo->i * 60) + $intervalo->s;
        } catch (Throwable) {
            return 0;
        }
    }

    private function consultar(string $videoId): ?MetadadosVideo
    {
        if (filled($this->chaveApi)) {
            $metadados = $this->consultarApi($videoId);

            if ($metadados !== null) {
                return $metadados;
            }
        }

        return $this->consultarOembed($videoId);
    }

    private function consultarApi(string $videoId): ?MetadadosVideo
    {
        try {
            $resposta = $this->http->timeout(5)->get('https://www.googleapis.com/youtube/v3/videos', [
                'id' => $videoId,
                'part' => 'snippet,contentDetails',
                'key' => $this->chaveApi,
            ]);
            $item = $resposta->successful() ? $resposta->json('items.0') : null;

            if (! is_array($item) || blank(data_get($item, 'snippet.title'))) {
                return null;
            }

            return new MetadadosVideo(
                titulo: (string) data_get($item, 'snippet.title'),
                duracaoSegundos: $this->converterDuracao((string) data_get($item, 'contentDetails.duration')),
                thumb: data_get($item, 'snippet.thumbnails.maxres.url')
                    ?? data_get($item, 'snippet.thumbnails.high.url'),
            );
        } catch (Throwable) {
            return null;
        }
    }

    private function consultarOembed(string $videoId): ?MetadadosVideo
    {
        try {
            $resposta = $this->http->timeout(5)->get('https://www.youtube.com/oembed', [
                'url' => "https://www.youtube.com/watch?v={$videoId}",
                'format' => 'json',
            ]);

            if (! $resposta->successful() || blank($resposta->json('title'))) {
                return null;
            }

            return new MetadadosVideo(
                titulo: (string) $resposta->json('title'),
                duracaoSegundos: 0,
                thumb: $resposta->json('thumbnail_url'),
                duracaoAutomatica: false,
            );
        } catch (Throwable) {
            return null;
        }
    }
}
