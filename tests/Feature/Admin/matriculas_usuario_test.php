<?php

declare(strict_types=1);

use App\Acoes\Matricula\CancelarMatricula;
use App\Acoes\Matricula\MatricularAluno;
use App\Enums\OrigemMatricula;
use App\Enums\SituacaoCurso;
use App\Enums\SituacaoMatricula;
use App\Models\Curso;
use App\Models\Usuario;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    Role::findOrCreate('admin', 'web');
    $this->admin = Usuario::factory()->create();
    $this->admin->assignRole('admin');
});

it('exibe a aba de matriculas na ficha do aluno', function (): void {
    $aluno = Usuario::factory()->create();

    $this->actingAs($this->admin)
        ->withoutVite()
        ->get("/admin/usuarios/{$aluno->id}")
        ->assertOk()
        ->assertSee('relation-managers.matriculas-relation-manager', false);
});

it('admin matricula e cancela aluno com auditoria', function (): void {
    $aluno = Usuario::factory()->create();
    $curso = Curso::factory()->create(['situacao' => SituacaoCurso::Publicado]);

    $matricula = app(MatricularAluno::class)->executar($aluno, $curso, OrigemMatricula::Admin, $this->admin);
    expect($matricula->origem)->toBe(OrigemMatricula::Admin)
        ->and(Activity::query()->where('subject_id', $matricula->id)->where('description', 'matricula_criada')->exists())->toBeTrue();

    $matricula = app(CancelarMatricula::class)->executar($matricula, $this->admin);
    expect($matricula->situacao)->toBe(SituacaoMatricula::Cancelada)
        ->and(Activity::query()->where('subject_id', $matricula->id)->where('description', 'matricula_cancelada')->exists())->toBeTrue();
});
