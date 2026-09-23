<?php

declare(strict_types=1);

use App\Acoes\Curso\ArquivarCurso;
use App\Acoes\Curso\PublicarCurso;
use App\Acoes\Curso\ReordenarCurriculo;
use App\Acoes\Curso\SalvarAula;
use App\Enums\NivelCurso;
use App\Enums\SituacaoAula;
use App\Excecoes\CursoIncompleto;
use App\Filament\Pages\ConstrutorCurriculo;
use App\Filament\Resources\Cursos\CursoResource;
use App\Filament\Resources\Cursos\Pages\CreateCurso;
use App\Models\Aula;
use App\Models\Categoria;
use App\Models\Curso;
use App\Models\Matricula;
use App\Models\Modulo;
use App\Models\Usuario;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    foreach (['aluno', 'instrutor', 'admin'] as $papel) {
        Role::findOrCreate($papel, 'web');
    }
});

it('instrutor não edita curso de outro instrutor', function (): void {
    $instrutor = Usuario::factory()->create();
    $outro = Usuario::factory()->create();
    $instrutor->assignRole('instrutor');
    $outro->assignRole('instrutor');
    $cursoProprio = Curso::factory()->for($instrutor, 'responsavel')->create();
    $cursoOutro = Curso::factory()->for($outro, 'responsavel')->create();

    expect(Gate::forUser($instrutor)->allows('update', $cursoProprio))->toBeTrue()
        ->and(Gate::forUser($instrutor)->allows('update', $cursoOutro))->toBeFalse();

    $this->actingAs($instrutor);
    expect(CursoResource::getEloquentQuery()->pluck('id')->all())->toBe([$cursoProprio->id]);
});

it('admin só exclui curso sem alunos matriculados', function (): void {
    $admin = Usuario::factory()->create();
    $admin->assignRole('admin');
    $cursoSemAlunos = Curso::factory()->create();
    $cursoComAlunos = Curso::factory()->create();
    Matricula::factory()->for($cursoComAlunos, 'curso')->create();

    expect(Gate::forUser($admin)->allows('delete', $cursoSemAlunos))->toBeTrue()
        ->and(Gate::forUser($admin)->allows('delete', $cursoComAlunos))->toBeFalse();
});

it('publicar curso incompleto falha e lista todas as pendências (RN-09)', function (): void {
    $curso = Curso::factory()->create([
        'titulo' => '',
        'categoria_id' => null,
        'capa_caminho' => null,
    ]);

    try {
        app(PublicarCurso::class)->executar($curso);
        $this->fail('A publicação deveria falhar.');
    } catch (CursoIncompleto $erro) {
        expect($erro->pendencias)->toHaveCount(4)
            ->and(implode(' ', $erro->pendencias))
            ->toContain('título')
            ->toContain('categoria')
            ->toContain('aula')
            ->toContain('capa');
    }
});

it('salvar aula grava a descrição editada no editor de texto rico', function (): void {
    $admin = Usuario::factory()->create();
    $admin->assignRole('admin');
    $curso = Curso::factory()->for($admin, 'responsavel')->create();
    $modulo = Modulo::factory()->for($curso)->create();
    $aula = Aula::factory()->for($modulo)->create(['descricao' => '<p>conteúdo original</p>']);

    Livewire::actingAs($admin)
        ->test(ConstrutorCurriculo::class, ['registro' => (string) $curso->id])
        ->call('selecionarAula', $aula->id)
        ->fillForm(['descricao' => '<p><strong>negrito</strong></p>'], 'descricaoAulaForm')
        ->call('salvarAula')
        ->assertHasNoErrors();

    expect($aula->fresh()->descricao)->toBe('<p><strong>negrito</strong></p>');
});

it('reordenar currículo persiste posições de módulos e aulas', function (): void {
    $curso = Curso::factory()->create();
    $primeiro = Modulo::factory()->for($curso)->create(['posicao' => 0]);
    $segundo = Modulo::factory()->for($curso)->create(['posicao' => 1]);
    $aulaUm = Aula::factory()->for($primeiro)->create(['posicao' => 0]);
    $aulaDois = Aula::factory()->for($segundo)->create(['posicao' => 0]);

    app(ReordenarCurriculo::class)->executar($curso, [
        ['id' => $segundo->id, 'aulas' => []],
        ['id' => $primeiro->id, 'aulas' => [$aulaDois->id, $aulaUm->id]],
    ]);

    expect($segundo->refresh()->posicao)->toBe(0)
        ->and($primeiro->refresh()->posicao)->toBe(1)
        ->and($aulaDois->refresh()->modulo_id)->toBe($primeiro->id)
        ->and($aulaDois->posicao)->toBe(0)
        ->and($aulaUm->refresh()->posicao)->toBe(1);
});

it('publica curso completo e recalcula os caches (RN-09)', function (): void {
    $curso = Curso::factory()->create(['capa_caminho' => null]);
    $modulo = Modulo::factory()->for($curso)->create();
    Aula::factory()->for($modulo)->create([
        'situacao' => SituacaoAula::Publicada,
        'duracao_segundos' => 735,
        'video_id' => 'dQw4w9WgXcQ',
    ]);

    $curso = app(PublicarCurso::class)->executar($curso);

    expect($curso->situacao->value)->toBe('publicado')
        ->and($curso->total_aulas)->toBe(1)
        ->and($curso->minutos_estimados)->toBe(13)
        ->and($curso->publicado_em)->not->toBeNull();
});

it('criar curso redireciona direto para o construtor de currículo', function (): void {
    $admin = Usuario::factory()->create();
    $admin->assignRole('admin');
    $categoria = Categoria::factory()->create();

    $componente = Livewire::actingAs($admin)
        ->test(CreateCurso::class)
        ->fillForm([
            'titulo' => 'Curso de Teste',
            'slug' => 'curso-de-teste',
            'categoria_id' => $categoria->id,
            'nivel' => NivelCurso::Basico->value,
            'responsavel_id' => $admin->id,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $curso = Curso::query()->where('slug', 'curso-de-teste')->firstOrFail();

    $componente->assertRedirect(ConstrutorCurriculo::getUrl(['registro' => $curso->id]));
});

it('admin acessa os resources e o construtor de currículo', function (): void {
    $admin = Usuario::factory()->create();
    $admin->assignRole('admin');
    $curso = Curso::factory()->for($admin, 'responsavel')->create();
    $this->actingAs($admin)->withoutVite();

    $this->get('/admin/cursos')->assertOk();
    $this->get('/admin/cursos/create')->assertOk();
    $this->get('/admin/categorias')->assertOk();
    $this->get('/admin/organizacoes')->assertOk();
    $this->get("/admin/cursos/{$curso->id}/curriculo")->assertOk();
});

it('publicar curso registra a atividade no log de auditoria', function (): void {
    $admin = Usuario::factory()->create();
    $admin->assignRole('admin');
    $curso = Curso::factory()->create(['capa_caminho' => null]);
    $modulo = Modulo::factory()->for($curso)->create();
    Aula::factory()->for($modulo)->create([
        'situacao' => SituacaoAula::Publicada,
        'duracao_segundos' => 735,
        'video_id' => 'dQw4w9WgXcQ',
    ]);

    app(PublicarCurso::class)->executar($curso, $admin);

    expect(Activity::query()->where('subject_type', Curso::class)->where('subject_id', $curso->id)->where('causer_id', $admin->id)->count())->toBe(1);
});

it('arquivar curso registra a atividade no log de auditoria', function (): void {
    $admin = Usuario::factory()->create();
    $admin->assignRole('admin');
    $curso = Curso::factory()->create();

    app(ArquivarCurso::class)->executar($curso, $admin);

    expect(Activity::query()->where('subject_type', Curso::class)->where('subject_id', $curso->id)->where('causer_id', $admin->id)->count())->toBe(1);
});

it('publicar aula dispara o recálculo de caches do curso', function (): void {
    $curso = Curso::factory()->create();
    $modulo = Modulo::factory()->for($curso)->create();
    $aula = Aula::factory()->for($modulo)->create([
        'situacao' => SituacaoAula::Rascunho,
        'duracao_segundos' => 600,
    ]);

    app(SalvarAula::class)->executar($aula, [
        'titulo' => $aula->titulo,
        'duracao_segundos' => 600,
        'situacao' => SituacaoAula::Publicada,
    ]);

    expect($curso->refresh()->total_aulas)->toBe(1)
        ->and($curso->minutos_estimados)->toBe(10);
});
