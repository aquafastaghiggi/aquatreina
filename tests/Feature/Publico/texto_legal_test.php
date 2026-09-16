<?php

declare(strict_types=1);

use App\Models\TextoLegal;
use Database\Seeders\TextoLegalSeeder;

it('publica os termos e a privacidade padrão sem aviso provisório', function (): void {
    $this->seed(TextoLegalSeeder::class);

    $this->get(route('termos'))
        ->assertOk()
        ->assertSee('Uso permitido')
        ->assertSee('universidade@aquafast.com.br')
        ->assertDontSee('Texto jurídico provisório');

    $this->get(route('privacidade'))
        ->assertOk()
        ->assertSee('Direitos do titular')
        ->assertSee('O cadastro público não solicita empresa, cargo')
        ->assertDontSee('Texto jurídico provisório');

    expect(TextoLegal::query()->count())->toBe(2);
});

it('não sobrescreve uma versão legal já existente', function (): void {
    TextoLegal::query()->create([
        'tipo' => 'termos',
        'versao' => 1,
        'conteudo' => 'Texto aprovado pelo jurídico.',
        'publicado_em' => now()->subDay(),
    ]);

    $this->seed(TextoLegalSeeder::class);

    expect(TextoLegal::vigente('termos')?->conteudo)->toBe('Texto aprovado pelo jurídico.')
        ->and(TextoLegal::vigente('privacidade'))->not->toBeNull();
});
