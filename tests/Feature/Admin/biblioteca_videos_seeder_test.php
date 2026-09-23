<?php

declare(strict_types=1);

use App\Enums\SituacaoAula;
use App\Enums\SituacaoCurso;
use App\Enums\SituacaoUsuario;
use App\Models\Aula;
use App\Models\Curso;
use App\Models\Usuario;
use Database\Seeders\BibliotecaVideosSeeder;
use Database\Seeders\TrilhaProdutoSeeder;
use Spatie\Permission\Models\Role;

it('publica automaticamente os produtos que ganham video e organiza aulas de forma idempotente', function (): void {
    Role::findOrCreate('admin', 'web');
    Usuario::factory()->create(['situacao' => SituacaoUsuario::Ativo])->assignRole('admin');

    $this->seed(TrilhaProdutoSeeder::class);
    $this->seed(BibliotecaVideosSeeder::class);
    $this->seed(BibliotecaVideosSeeder::class);

    expect(Curso::query()->where('titulo', 'Poder O2')->first()->situacao)->toBe(SituacaoCurso::Publicado)
        ->and(Curso::query()->where('titulo', 'Multiuso')->first()->situacao)->toBe(SituacaoCurso::Publicado)
        ->and(Curso::query()->where('titulo', 'Amaciantes')->first()->situacao)->toBe(SituacaoCurso::Rascunho)
        ->and(Curso::query()->where('titulo', 'Aromatizador de Ambientes')->first()->situacao)->toBe(SituacaoCurso::Rascunho);

    $aulasComVideo = Aula::query()->whereNotNull('video_id')->get();
    expect($aulasComVideo)->toHaveCount(9)
        ->and($aulasComVideo->every(fn (Aula $aula): bool => $aula->situacao === SituacaoAula::Publicada))->toBeTrue();

    $mancha = Aula::query()->where('slug', 'mancha-em-uniforme')->first();
    expect($mancha->modulo->curso->titulo)->toBe('Multiuso');
});
