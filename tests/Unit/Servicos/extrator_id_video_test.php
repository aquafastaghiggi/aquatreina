<?php

declare(strict_types=1);

use App\Servicos\Video\ExtratorIdVideo;

it('extrai o id dos formatos válidos', function (string $entrada): void {
    expect((new ExtratorIdVideo)->extrair($entrada))->toBe('dQw4w9WgXcQ');
})->with([
    'watch' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
    'curto' => 'https://youtu.be/dQw4w9WgXcQ',
    'embed' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
    'shorts' => 'https://www.youtube.com/shorts/dQw4w9WgXcQ',
    'live' => 'https://www.youtube.com/live/dQw4w9WgXcQ',
    'somente id' => 'dQw4w9WgXcQ',
    'com parâmetros' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ&t=42s&list=PL123',
]);

it('rejeita entradas inválidas', function (string $entrada): void {
    expect((new ExtratorIdVideo)->extrair($entrada))->toBeNull();
})->with([
    'id curto' => 'abc123',
    'caractere inválido' => 'dQw4w9WgXc!',
    'domínio falso' => 'https://notyoutube.com/watch?v=dQw4w9WgXcQ',
    'url sem id' => 'https://www.youtube.com/watch',
]);
