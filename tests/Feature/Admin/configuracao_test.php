<?php

declare(strict_types=1);

use App\Filament\Pages\Configuracoes;
use App\Models\Configuracao;
use App\Models\Usuario;
use Livewire\Livewire;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Role;

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

it('salvar configuracoes registra a atividade no log de auditoria', function (): void {
    Role::findOrCreate('admin', 'web');
    $admin = Usuario::factory()->create();
    $admin->assignRole('admin');

    Livewire::actingAs($admin)
        ->test(Configuracoes::class)
        ->set('percentualConclusao', 80)
        ->set('intervaloPing', 15)
        ->set('textoBoasVindas', 'Bem-vindo!')
        ->set('termos', 'Termos atualizados.')
        ->set('privacidade', 'Privacidade atualizada.')
        ->call('salvar');

    expect(Activity::query()->where('causer_id', $admin->id)->where('description', 'Configurações atualizadas')->count())->toBe(1);
});
