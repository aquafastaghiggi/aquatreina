<?php

declare(strict_types=1);

use App\Enums\SituacaoAula;
use App\Enums\SituacaoCurso;
use App\Enums\SituacaoMatricula;
use App\Listeners\EnviarBoasVindasAoAlunoMatriculado;
use App\Models\Aula;
use App\Models\Curso;
use App\Models\Matricula;
use App\Models\Modulo;
use App\Models\Usuario;
use Illuminate\Events\CallQueuedListener;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Queue;

it('mantem uma unica matricula ao receber dois pedidos para o mesmo curso (RN-05)', function (): void {
    $aluno = Usuario::factory()->create();
    $curso = Curso::factory()->create(['situacao' => SituacaoCurso::Publicado, 'publicado_em' => now()]);

    $this->actingAs($aluno)->post(route('app.inscrever', $curso))->assertRedirect(route('app.curso', $curso));
    $this->actingAs($aluno)->post(route('app.inscrever', $curso))->assertRedirect(route('app.curso', $curso));

    expect(Matricula::query()->whereBelongsTo($aluno, 'usuario')->whereBelongsTo($curso)->count())->toBe(1);
});

it('nao permite matricula em curso que nao esteja publicado (RN-05)', function (): void {
    $aluno = Usuario::factory()->create();
    $curso = Curso::factory()->create(['situacao' => SituacaoCurso::Rascunho]);

    $this->actingAs($aluno)->post(route('app.inscrever', $curso))->assertNotFound();
    $this->assertDatabaseMissing('matriculas', ['usuario_id' => $aluno->id, 'curso_id' => $curso->id]);
});

it('reativa matricula cancelada preservando o progresso (RN-05)', function (): void {
    $aluno = Usuario::factory()->create();
    $curso = Curso::factory()->create(['situacao' => SituacaoCurso::Publicado, 'publicado_em' => now()]);
    $modulo = Modulo::factory()->for($curso)->create();
    $aula = Aula::factory()->for($modulo)->create(['situacao' => SituacaoAula::Publicada]);
    $matricula = Matricula::factory()->for($aluno, 'usuario')->for($curso)->create([
        'situacao' => SituacaoMatricula::Cancelada,
        'percentual_progresso' => 47,
        'ultima_aula_id' => $aula->id,
    ]);

    $this->actingAs($aluno)->post(route('app.inscrever', $curso))->assertRedirect(route('app.curso', $curso));

    $matricula->refresh();
    expect($matricula->situacao)->toBe(SituacaoMatricula::Ativa)
        ->and($matricula->percentual_progresso)->toBe(47)
        ->and($matricula->ultima_aula_id)->toBe($aula->id);
});

it('coloca o envio de boas vindas na fila ao matricular', function (): void {
    Queue::fake();
    $aluno = Usuario::factory()->create();
    $curso = Curso::factory()->create(['situacao' => SituacaoCurso::Publicado]);

    $this->actingAs($aluno)->post(route('app.inscrever', $curso))->assertRedirect();

    Queue::assertPushed(CallQueuedListener::class, fn (CallQueuedListener $job): bool => $job->class === EnviarBoasVindasAoAlunoMatriculado::class);
});

it('matricula em curso fora da trilha de produtos nao aparece na grade do painel', function (): void {
    $aluno = Usuario::factory()->create();
    $curso = Curso::factory()->create(['titulo' => 'Operação segura Aquafast', 'situacao' => SituacaoCurso::Publicado]);
    Matricula::factory()->for($aluno, 'usuario')->for($curso)->create();

    $this->actingAs($aluno)->get(route('app.painel'))
        ->assertOk()
        ->assertDontSee('Operação segura Aquafast');
});

it('nao permite que aluno veja matricula de outra pessoa', function (): void {
    $dono = Usuario::factory()->create();
    $outroAluno = Usuario::factory()->create();
    $matricula = Matricula::factory()->for($dono, 'usuario')->create();

    expect(Gate::forUser($outroAluno)->denies('view', $matricula))->toBeTrue();
});
