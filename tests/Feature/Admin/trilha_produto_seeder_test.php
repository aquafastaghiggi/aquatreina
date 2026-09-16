<?php

declare(strict_types=1);

use App\Enums\SituacaoCurso;
use App\Enums\SituacaoUsuario;
use App\Models\Categoria;
use App\Models\Curso;
use App\Models\Usuario;
use Database\Seeders\TrilhaProdutoSeeder;
use Spatie\Permission\Models\Role;

it('cria a categoria e os seis produtos em rascunho de forma idempotente', function (): void {
    Role::findOrCreate('admin', 'web');
    Usuario::factory()->create(['situacao' => SituacaoUsuario::Ativo])->assignRole('admin');

    $this->seed(TrilhaProdutoSeeder::class);
    $this->seed(TrilhaProdutoSeeder::class);

    expect(Categoria::query()->where('slug', config('treina.categoria_trilhas_produto_slug'))->count())->toBe(1)
        ->and(Curso::query()->where('categoria_id', Categoria::query()->where('slug', config('treina.categoria_trilhas_produto_slug'))->value('id'))->count())->toBe(6)
        ->and(Curso::query()->where('titulo', 'Poder O2')->first()->situacao)->toBe(SituacaoCurso::Rascunho);
});
