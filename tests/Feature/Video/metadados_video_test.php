<?php

declare(strict_types=1);

use App\Acoes\Curso\SalvarAula;
use App\Models\Aula;
use App\Models\Modulo;
use App\Servicos\Video\ExtratorIdVideo;
use App\Servicos\Video\LeitorMetadadosVideo;
use App\Servicos\Video\YoutubeProvedor;
use Illuminate\Contracts\Cache\Factory as FabricaCache;
use Illuminate\Http\Client\Factory as ClienteHttp;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

it('converte PT12M15S em 735 segundos', function (): void {
    $leitor = new LeitorMetadadosVideo(
        app(ClienteHttp::class), app(FabricaCache::class), 'chave-de-teste',
    );

    expect($leitor->converterDuracao('PT12M15S'))->toBe(735);
});

it('lê título e duração pela API de dados', function (): void {
    Cache::flush();
    Http::fake([
        'www.googleapis.com/*' => Http::response(['items' => [[
            'snippet' => ['title' => 'Treinamento Aquafast', 'thumbnails' => []],
            'contentDetails' => ['duration' => 'PT12M15S'],
        ]]]),
    ]);
    $leitor = new LeitorMetadadosVideo(
        app(ClienteHttp::class), app(FabricaCache::class), 'chave-de-teste',
    );

    $metadados = $leitor->ler('dQw4w9WgXcQ');

    expect($metadados?->titulo)->toBe('Treinamento Aquafast')
        ->and($metadados?->duracaoSegundos)->toBe(735);
});

it('cai para oEmbed quando a chave da API está ausente', function (): void {
    Cache::flush();
    Http::fake([
        'www.youtube.com/oembed*' => Http::response([
            'title' => 'Título pelo oEmbed',
            'thumbnail_url' => 'https://i.ytimg.com/thumb.jpg',
        ]),
    ]);
    $leitor = new LeitorMetadadosVideo(
        app(ClienteHttp::class), app(FabricaCache::class), null,
    );

    $metadados = $leitor->ler('dQw4w9WgXcQ');

    expect($metadados?->titulo)->toBe('Título pelo oEmbed')
        ->and($metadados?->duracaoSegundos)->toBe(0)
        ->and($metadados?->duracaoAutomatica)->toBeFalse();
    Http::assertNotSent(fn ($request): bool => str_contains($request->url(), 'googleapis.com'));
});

it('salva a aula mesmo quando as duas fontes de metadados estão indisponíveis', function (): void {
    Cache::flush();
    Http::fake(fn () => Http::response([], 503));
    $modulo = Modulo::factory()->create();
    $aula = new Aula(['modulo_id' => $modulo->id]);

    $aula = app(SalvarAula::class)->executar($aula, [
        'modulo_id' => $modulo->id,
        'titulo' => 'Título manual',
        'link_video' => 'https://youtu.be/dQw4w9WgXcQ',
        'duracao_segundos' => 0,
    ]);

    expect($aula->exists)->toBeTrue()
        ->and($aula->titulo)->toBe('Título manual')
        ->and($aula->video_id)->toBe('dQw4w9WgXcQ')
        ->and($aula->duracao_segundos)->toBe(0);
});

it('mantém os metadados em cache por 24 horas', function (): void {
    Cache::flush();
    Http::fake(['www.youtube.com/oembed*' => Http::response(['title' => 'Título em cache'])]);
    $leitor = new LeitorMetadadosVideo(app(ClienteHttp::class), app(FabricaCache::class), null);

    $leitor->ler('dQw4w9WgXcQ');
    $leitor->ler('dQw4w9WgXcQ');
    Http::assertSentCount(1);

    $this->travel(25)->hours();
    $leitor->ler('dQw4w9WgXcQ');
    Http::assertSentCount(2);
});

it('gera embed privado com os parâmetros exigidos e oferece thumb de fallback', function (): void {
    $leitor = new LeitorMetadadosVideo(app(ClienteHttp::class), app(FabricaCache::class), null);
    $provedor = new YoutubeProvedor(new ExtratorIdVideo, $leitor, 'https://treinamentos.aquafast.com.br');
    $url = $provedor->urlEmbed('dQw4w9WgXcQ');

    expect($url)->toStartWith('https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ?')
        ->toContain('rel=0', 'modestbranding=1', 'playsinline=1', 'enablejsapi=1', 'cc_lang_pref=pt')
        ->and(urldecode($url))->toContain('origin=https://treinamentos.aquafast.com.br')
        ->and($provedor->urlThumb('dQw4w9WgXcQ'))->toContain('maxresdefault.jpg')
        ->and($provedor->urlThumbFallback('dQw4w9WgXcQ'))->toContain('hqdefault.jpg');
});
