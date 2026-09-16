<?php

declare(strict_types=1);

use App\Enums\SituacaoCurso;
use App\Models\Categoria;
use App\Models\Curso;
use App\Models\Usuario;

it('mostra as secoes institucionais e a formula de conteudo no painel', function (): void {
    $aluno = Usuario::factory()->create();

    $this->actingAs($aluno)->get(route('app.painel'))
        ->assertOk()
        ->assertSee('Bem-vindo à Aquafast')
        ->assertSee('Guaporé')
        ->assertSee('A fórmula de um bom conteúdo Aquafast')
        ->assertSee('Mostre a dor')
        ->assertSee('Mostre onde comprar')
        ->assertSee('Boas práticas para seus conteúdos')
        ->assertSee('Cresça com a Aquafast')
        ->assertSee('Crie. Ensine. Venda. Cresça com a Aquafast.', false);
});

it('lista os produtos publicados da trilha aprenda na pratica no painel', function (): void {
    $aluno = Usuario::factory()->create();
    $categoria = Categoria::factory()->create([
        'nome' => 'Aprenda na Prática',
        'slug' => config('treina.categoria_trilhas_produto_slug'),
    ]);
    $produto = Curso::factory()->for($categoria)->create([
        'titulo' => 'Poder O2',
        'situacao' => SituacaoCurso::Publicado,
    ]);

    $this->actingAs($aluno)->get(route('app.painel'))
        ->assertOk()
        ->assertSee('Agora vamos conhecer os produtos?')
        ->assertSee('Poder O2');

    expect($produto)->not->toBeNull();
});

it('nao lista produto em rascunho no painel', function (): void {
    $aluno = Usuario::factory()->create();
    $categoria = Categoria::factory()->create(['slug' => config('treina.categoria_trilhas_produto_slug')]);
    Curso::factory()->for($categoria)->create([
        'titulo' => 'Multiuso Rascunho',
        'situacao' => SituacaoCurso::Rascunho,
    ]);

    $this->actingAs($aluno)->get(route('app.painel'))
        ->assertOk()
        ->assertDontSee('Multiuso Rascunho');
});
