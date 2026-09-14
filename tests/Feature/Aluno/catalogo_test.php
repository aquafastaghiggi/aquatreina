<?php

declare(strict_types=1);

use App\Enums\SituacaoCurso;
use App\Livewire\Aluno\Catalogo;
use App\Models\Categoria;
use App\Models\Curso;
use App\Models\Usuario;
use Livewire\Livewire;

it('lista apenas cursos publicados no catalogo', function (): void {
    $aluno = Usuario::factory()->create();
    Curso::factory()->create(['titulo' => 'Curso publicado visível', 'situacao' => SituacaoCurso::Publicado]);
    Curso::factory()->create(['titulo' => 'Curso rascunho oculto', 'situacao' => SituacaoCurso::Rascunho]);

    Livewire::actingAs($aluno)->test(Catalogo::class)
        ->assertSee('Curso publicado visível')
        ->assertDontSee('Curso rascunho oculto');
});

it('filtra o catalogo por busca categoria e nivel', function (): void {
    $aluno = Usuario::factory()->create();
    $categoria = Categoria::factory()->create(['nome' => 'Operação']);
    Curso::factory()->for($categoria)->create([
        'titulo' => 'Segurança no estoque',
        'situacao' => SituacaoCurso::Publicado,
        'nivel' => 'basico',
    ]);
    Curso::factory()->create(['titulo' => 'Atendimento comercial', 'situacao' => SituacaoCurso::Publicado]);

    Livewire::actingAs($aluno)->test(Catalogo::class)
        ->set('busca', 'estoque')
        ->set('categoria', $categoria->slug)
        ->set('nivel', 'basico')
        ->assertSee('Segurança no estoque')
        ->assertDontSee('Atendimento comercial');
});

it('mostra estado vazio quando nenhum curso corresponde aos filtros', function (): void {
    $aluno = Usuario::factory()->create();

    Livewire::actingAs($aluno)->test(Catalogo::class)
        ->set('busca', 'inexistente')
        ->assertSee('Nenhum curso encontrado');
});
