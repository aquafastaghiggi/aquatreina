<?php

declare(strict_types=1);

use App\Models\Configuracao;

it('grava a descricao de uma chave de configuracao', function (): void {
    Configuracao::query()->create([
        'chave' => 'percentual_conclusao',
        'valor' => '90',
        'tipo' => 'int',
        'descricao' => 'Percentual assistido para concluir a aula automaticamente.',
    ]);

    $configuracao = Configuracao::query()->where('chave', 'percentual_conclusao')->firstOrFail();
    expect($configuracao->descricao)->toBe('Percentual assistido para concluir a aula automaticamente.');
});
