<?php

declare(strict_types=1);

use App\Enums\SituacaoAula;
use App\Enums\SituacaoCurso;
use App\Models\Aula;
use App\Models\Curso;
use App\Models\Modulo;

it('retorna 404 para pagina de curso arquivado', function (): void {
    $curso = Curso::factory()->create(['situacao' => SituacaoCurso::Arquivado]);

    $this->get(route('cursos.mostrar', $curso))->assertNotFound();
});

it('abre amostra gratuita sem login e bloqueia aula comum', function (): void {
    $curso = Curso::factory()->create(['situacao' => SituacaoCurso::Publicado]);
    $modulo = Modulo::factory()->for($curso)->create();
    $amostra = Aula::factory()->for($modulo)->create([
        'amostra_gratuita' => true,
        'situacao' => SituacaoAula::Publicada,
    ]);
    $restrita = Aula::factory()->for($modulo)->create([
        'amostra_gratuita' => false,
        'situacao' => SituacaoAula::Publicada,
    ]);
    $rascunho = Aula::factory()->for($modulo)->create([
        'amostra_gratuita' => true,
        'situacao' => SituacaoAula::Rascunho,
    ]);

    $this->get(route('cursos.amostra', [$curso, $amostra]))
        ->assertOk()
        ->assertSee($amostra->titulo);
    $this->get(route('cursos.amostra', [$curso, $restrita]))->assertNotFound();
    $this->get(route('cursos.amostra', [$curso, $rascunho]))->assertNotFound();
});

it('convida visitante a criar conta na pagina publica do curso', function (): void {
    $curso = Curso::factory()->create(['situacao' => SituacaoCurso::Publicado]);

    $this->get(route('cursos.mostrar', $curso))
        ->assertOk()
        ->assertSee('Criar conta para me inscrever')
        ->assertSee(route('register'), false)
        ->assertSee('<meta property="og:title"', false)
        ->assertSee('<meta name="description"', false);
});
