<?php

declare(strict_types=1);

use App\Acoes\Curso\PublicarCurso;
use App\Acoes\Curso\ReordenarCurriculo;
use App\Acoes\Curso\SalvarAula;
use App\Enums\SituacaoAula;
use App\Excecoes\CursoIncompleto;
use App\Filament\Resources\Cursos\CursoResource;
use App\Models\Aula;
use App\Models\Curso;
use App\Models\Modulo;
use App\Models\Usuario;
use Illuminate\Support\Facades\Gate;
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
