<?php

declare(strict_types=1);

use App\Models\Organizacao;
use Illuminate\Database\QueryException;

it('nao permite duas organizacoes com o mesmo cnpj', function (): void {
    Organizacao::factory()->create(['cnpj' => '12345678000199']);

    expect(fn () => Organizacao::factory()->create(['cnpj' => '12345678000199']))
        ->toThrow(QueryException::class);
});

it('permite mais de uma organizacao sem cnpj informado', function (): void {
    Organizacao::factory()->create(['cnpj' => null]);
    Organizacao::factory()->create(['cnpj' => null]);

    expect(Organizacao::query()->whereNull('cnpj')->count())->toBe(2);
});
