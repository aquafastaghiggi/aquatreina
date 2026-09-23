<?php

declare(strict_types=1);

use App\Acoes\Curso\SalvarAula;
use App\Acoes\Progresso\ConcluirAula;
use App\Acoes\Progresso\RecalcularProgresso;
use App\Acoes\Progresso\VerificarConclusaoDoCurso;
use App\Enums\SituacaoAula;
use App\Enums\SituacaoCurso;
use App\Enums\SituacaoMatricula;
use App\Jobs\ConferirProgressoDiario;
use App\Listeners\EnviarEmailAoConcluirCurso;
use App\Livewire\Aluno\SalaDeAula;
use App\Models\Aula;
use App\Models\Curso;
use App\Models\Matricula;
use App\Models\Modulo;
use App\Models\ProgressoAula;
use App\Models\Usuario;
use Illuminate\Events\CallQueuedListener;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;

function cenarioConclusao(int $totalAulas = 1): array
{
    $aluno = Usuario::factory()->create();
    $curso = Curso::factory()->create(['situacao' => SituacaoCurso::Publicado]);
    $modulo = Modulo::factory()->for($curso)->create();
    $aulas = Aula::factory()->count($totalAulas)->for($modulo)->create([
        'situacao' => SituacaoAula::Publicada,
        'duracao_segundos' => 100,
    ]);
    $matricula = Matricula::factory()->for($aluno, 'usuario')->for($curso)->create();

    return compact('aluno', 'curso', 'modulo', 'aulas', 'matricula');
}

it('conclui automaticamente ao atingir noventa por cento do video (RN-01)', function (): void {
    Queue::fake();
    $cenario = cenarioConclusao();
    $aula = $cenario['aulas']->first();
    $progresso = ProgressoAula::factory()->for($cenario['matricula'])->for($aula)->create([
        'posicao_maxima' => 90,
        'segundos_assistidos' => 90,
    ]);

    app(ConcluirAula::class)->executar($cenario['matricula'], $aula);

    expect($progresso->fresh()->concluido_em)->not->toBeNull();
});

it('permite concluir uma aula manualmente (RN-01)', function (): void {
    Queue::fake();
    $cenario = cenarioConclusao();
    $aula = $cenario['aulas']->first();

    Livewire::actingAs($cenario['aluno'])
        ->test(SalaDeAula::class, ['curso' => $cenario['curso'], 'aula' => $aula])
        ->call('concluirManualmente')
        ->assertHasNoErrors();

    expect(ProgressoAula::query()->firstOrFail()->concluido_em)->not->toBeNull();
});

it('nao altera a data ao concluir a mesma aula duas vezes (RN-01)', function (): void {
    Queue::fake();
    $cenario = cenarioConclusao();
    $aula = $cenario['aulas']->first();
    $concluir = app(ConcluirAula::class);
    $primeiro = $concluir->executar($cenario['matricula'], $aula, true)->concluido_em;

    $this->travel(1)->hour();
    $segundo = $concluir->executar($cenario['matricula'], $aula, true)->concluido_em;

    expect($segundo->equalTo($primeiro))->toBeTrue();
});

it('aula sem duracao conclui apenas pelo caminho manual (RN-01)', function (): void {
    Queue::fake();
    $cenario = cenarioConclusao();
    $aula = $cenario['aulas']->first();
    $aula->update(['duracao_segundos' => 0]);
    $progresso = ProgressoAula::factory()->for($cenario['matricula'])->for($aula)->create([
        'posicao_maxima' => 100,
    ]);

    app(ConcluirAula::class)->executar($cenario['matricula'], $aula);
    expect($progresso->fresh()->concluido_em)->toBeNull();

    app(ConcluirAula::class)->executar($cenario['matricula'], $aula, true);
    expect($progresso->fresh()->concluido_em)->not->toBeNull();
});

it('concluir a ultima aula conclui a matricula (RN-04)', function (): void {
    Queue::fake();
    $cenario = cenarioConclusao(2);
    [$primeira, $ultima] = $cenario['aulas']->values();
    ProgressoAula::factory()->for($cenario['matricula'])->for($primeira)->create(['concluido_em' => now()]);

    app(ConcluirAula::class)->executar($cenario['matricula'], $ultima, true);

    expect($cenario['matricula']->fresh()->situacao)->toBe(SituacaoMatricula::Concluida)
        ->and($cenario['matricula']->fresh()->percentual_progresso)->toBe(100);

    Queue::assertPushed(
        CallQueuedListener::class,
        fn (CallQueuedListener $job): bool => $job->class === EnviarEmailAoConcluirCurso::class,
    );
});

it('publicar aula nova reabre matricula e preserva a conclusao anterior (RN-04)', function (): void {
    $cenario = cenarioConclusao(2);
    $concluidoEm = now()->subDay();
    $cenario['matricula']->update([
        'situacao' => SituacaoMatricula::Concluida,
        'percentual_progresso' => 100,
        'concluido_em' => $concluidoEm,
    ]);
    $concluidoEm = $cenario['matricula']->fresh()->concluido_em;
    foreach ($cenario['aulas'] as $aula) {
        ProgressoAula::factory()->for($cenario['matricula'])->for($aula)->create(['concluido_em' => now()]);
    }
    $nova = Aula::factory()->for($cenario['modulo'])->create([
        'situacao' => SituacaoAula::Rascunho,
        'video_id' => 'M7lc1UVf-VE',
    ]);

    app(SalvarAula::class)->executar($nova, [
        'titulo' => $nova->titulo,
        'situacao' => SituacaoAula::Publicada,
    ]);

    $matricula = $cenario['matricula']->fresh();
    expect($matricula->situacao)->toBe(SituacaoMatricula::Ativa)
        ->and($matricula->percentual_progresso)->toBe(67)
        ->and($matricula->concluido_em->equalTo($concluidoEm))->toBeTrue();
});

it('despublicar aula recalcula o percentual para cima (RN-04)', function (): void {
    Queue::fake();
    $cenario = cenarioConclusao(2);
    [$concluida, $pendente] = $cenario['aulas']->values();
    ProgressoAula::factory()->for($cenario['matricula'])->for($concluida)->create(['concluido_em' => now()]);
    $cenario['matricula']->update(['percentual_progresso' => 50]);

    app(SalvarAula::class)->executar($pendente, [
        'titulo' => $pendente->titulo,
        'situacao' => SituacaoAula::Rascunho,
    ]);

    expect($cenario['matricula']->fresh()->percentual_progresso)->toBe(100)
        ->and($cenario['matricula']->fresh()->situacao)->toBe(SituacaoMatricula::Concluida);
});

it('job diario corrige cache divergente', function (): void {
    Queue::fake();
    $cenario = cenarioConclusao();
    $aula = $cenario['aulas']->first();
    ProgressoAula::factory()->for($cenario['matricula'])->for($aula)->create(['concluido_em' => now()]);
    $cenario['matricula']->update(['percentual_progresso' => 12]);

    app(ConferirProgressoDiario::class)->handle(
        app(RecalcularProgresso::class),
        app(VerificarConclusaoDoCurso::class),
    );

    expect($cenario['matricula']->fresh()->percentual_progresso)->toBe(100);
});
