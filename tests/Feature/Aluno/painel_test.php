<?php

declare(strict_types=1);

use App\Enums\ProvedorVideo;
use App\Enums\SituacaoAula;
use App\Enums\SituacaoCurso;
use App\Models\Aula;
use App\Models\Categoria;
use App\Models\Curso;
use App\Models\Modulo;
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

it('lista os produtos publicados com video da trilha aprenda na pratica no painel', function (): void {
    $aluno = Usuario::factory()->create();
    $categoria = Categoria::factory()->create([
        'nome' => 'Aprenda na Prática',
        'slug' => config('treina.categoria_trilhas_produto_slug'),
    ]);
    $curso = Curso::factory()->for($categoria)->create([
        'titulo' => 'Poder O2',
        'situacao' => SituacaoCurso::Publicado,
    ]);
    $modulo = Modulo::factory()->for($curso)->create();
    Aula::factory()->for($modulo)->create(['situacao' => SituacaoAula::Publicada, 'provedor' => ProvedorVideo::Youtube, 'video_id' => 'abc12345678']);

    $this->actingAs($aluno)->get(route('app.painel'))
        ->assertOk()
        ->assertSee('Agora vamos conhecer os produtos?')
        ->assertSee('Poder O2');
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

it('nao lista produto publicado sem nenhum video no painel', function (): void {
    $aluno = Usuario::factory()->create();
    $categoria = Categoria::factory()->create(['slug' => config('treina.categoria_trilhas_produto_slug')]);
    Curso::factory()->for($categoria)->create([
        'titulo' => 'Amaciantes',
        'situacao' => SituacaoCurso::Publicado,
    ]);

    $this->actingAs($aluno)->get(route('app.painel'))
        ->assertOk()
        ->assertDontSee('Amaciantes');
});

it('card de produto com video embute o player direto, sem link pra pagina do curso', function (): void {
    $aluno = Usuario::factory()->create();
    $categoria = Categoria::factory()->create(['slug' => config('treina.categoria_trilhas_produto_slug')]);
    $curso = Curso::factory()->for($categoria)->create([
        'titulo' => 'Poder O2',
        'situacao' => SituacaoCurso::Publicado,
    ]);
    $modulo = Modulo::factory()->for($curso)->create();
    Aula::factory()->for($modulo)->create([
        'situacao' => SituacaoAula::Publicada,
        'provedor' => ProvedorVideo::Youtube,
        'video_id' => 'abc12345678',
    ]);

    $this->actingAs($aluno)->get(route('app.painel'))
        ->assertOk()
        ->assertSee('<iframe', false)
        ->assertSee('https://www.youtube-nocookie.com/embed/abc12345678', false)
        ->assertDontSee(route('cursos.mostrar', $curso), false);
});

it('produto com varios videos aparece em um card separado por video', function (): void {
    $aluno = Usuario::factory()->create();
    $categoria = Categoria::factory()->create(['slug' => config('treina.categoria_trilhas_produto_slug')]);
    $curso = Curso::factory()->for($categoria)->create([
        'titulo' => 'Poder O2',
        'situacao' => SituacaoCurso::Publicado,
    ]);
    $modulo = Modulo::factory()->for($curso)->create();
    Aula::factory()->for($modulo)->create(['situacao' => SituacaoAula::Publicada, 'provedor' => ProvedorVideo::Youtube, 'video_id' => 'video111111']);
    Aula::factory()->for($modulo)->create(['situacao' => SituacaoAula::Publicada, 'provedor' => ProvedorVideo::Youtube, 'video_id' => 'video222222']);
    Aula::factory()->for($modulo)->create(['situacao' => SituacaoAula::Rascunho, 'provedor' => ProvedorVideo::Youtube, 'video_id' => null]);

    $this->actingAs($aluno)->get(route('app.painel'))
        ->assertOk()
        ->assertSee('https://www.youtube-nocookie.com/embed/video111111', false)
        ->assertSee('https://www.youtube-nocookie.com/embed/video222222', false);
});
