<?php

declare(strict_types=1);

use App\Enums\SituacaoComentario;
use App\Enums\SituacaoCurso;
use App\Filament\Resources\Comentarios\ComentarioResource;
use App\Filament\Widgets\ConclusaoPorCurso;
use App\Filament\Widgets\PerguntasSemResposta;
use App\Models\Aula;
use App\Models\Comentario;
use App\Models\Curso;
use App\Models\Modulo;
use App\Models\Usuario;
use Spatie\Permission\Models\Role;

it('instrutor ve apenas perguntas dos proprios cursos no admin', function (): void {
    Role::findOrCreate('instrutor', 'web');
    $instrutor = Usuario::factory()->create();
    $instrutor->assignRole('instrutor');
    $outro = Usuario::factory()->create();
    $outro->assignRole('instrutor');
    $cursoProprio = Curso::factory()->for($instrutor, 'responsavel')->create(['situacao' => SituacaoCurso::Publicado]);
    $cursoAlheio = Curso::factory()->for($outro, 'responsavel')->create(['situacao' => SituacaoCurso::Publicado]);
    $aulaPropria = Aula::factory()->for(Modulo::factory()->for($cursoProprio))->create();
    $aulaAlheia = Aula::factory()->for(Modulo::factory()->for($cursoAlheio))->create();
    $perguntaPropria = Comentario::factory()->for($aulaPropria)->create();
    Comentario::factory()->for($aulaAlheia)->create();

    $this->actingAs($instrutor)
        ->get(ComentarioResource::getUrl('index'))
        ->assertOk();

    expect(ComentarioResource::getEloquentQuery()->pluck('comentarios.id')->all())
        ->toBe([$perguntaPropria->id]);
});

it('painel admin coloca perguntas sem resposta antes dos indicadores', function (): void {
    Role::findOrCreate('admin', 'web');
    $admin = Usuario::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin)->get('/admin')->assertOk();

    expect(PerguntasSemResposta::getSort())->toBeLessThan(ConclusaoPorCurso::getSort());
});

it('mostra no menu a contagem de perguntas pendentes de moderacao', function (): void {
    Role::findOrCreate('admin', 'web');
    $admin = Usuario::factory()->create();
    $admin->assignRole('admin');
    $curso = Curso::factory()->create(['situacao' => SituacaoCurso::Publicado]);
    $aula = Aula::factory()->for(Modulo::factory()->for($curso))->create();
    Comentario::factory()->for($aula)->create(['situacao' => SituacaoComentario::Pendente]);
    Comentario::factory()->for($aula)->create(['situacao' => SituacaoComentario::Pendente]);
    Comentario::factory()->for($aula)->create(['situacao' => SituacaoComentario::Aprovado]);

    $this->actingAs($admin);

    expect(ComentarioResource::getNavigationBadge())->toBe('2');
});
