<?php

declare(strict_types=1);

use App\Acoes\Progresso\RegistrarProgresso;
use App\Enums\SituacaoAula;
use App\Enums\SituacaoCurso;
use App\Enums\SituacaoMatricula;
use App\Models\Aula;
use App\Models\Curso;
use App\Models\Matricula;
use App\Models\Modulo;
use App\Models\ProgressoAula;
use App\Models\Usuario;

function cenarioProgresso(): array
{
    $aluno = Usuario::factory()->create();
    $curso = Curso::factory()->create(['situacao' => SituacaoCurso::Publicado]);
    $modulo = Modulo::factory()->for($curso)->create();
    $aula = Aula::factory()->for($modulo)->create([
        'situacao' => SituacaoAula::Publicada,
        'duracao_segundos' => 100,
    ]);
    $matricula = Matricula::factory()->for($aluno, 'usuario')->for($curso)->create([
        'situacao' => SituacaoMatricula::Ativa,
    ]);

    return compact('aluno', 'curso', 'modulo', 'aula', 'matricula');
}

it('soma tempo e atualiza a posicao durante avanco normal (RN-02)', function (): void {
    $cenario = cenarioProgresso();

    $resposta = $this->actingAs($cenario['aluno'])->postJson(route('app.progresso'), [
        'aula_id' => $cenario['aula']->id,
        'posicao' => 10,
    ])->assertOk();

    $resposta
        ->assertJsonStructure(['posicao_maxima', 'segundos_assistidos', 'concluida', 'percentual_curso'])
        ->assertJsonPath('posicao_maxima', 10)
        ->assertJsonPath('segundos_assistidos', 10);

    expect($cenario['matricula']->fresh()->ultima_aula_id)->toBe($cenario['aula']->id);
});

it('ignora retorno no video sem diminuir a posicao maxima (RN-02)', function (): void {
    $cenario = cenarioProgresso();
    ProgressoAula::factory()->for($cenario['matricula'])->for($cenario['aula'])->create([
        'posicao_maxima' => 50,
        'segundos_assistidos' => 50,
    ]);

    app(RegistrarProgresso::class)->executar($cenario['aluno'], $cenario['aula'], 40);

    $progresso = ProgressoAula::query()->firstOrFail();
    expect($progresso->posicao_maxima)->toBe(50)
        ->and($progresso->segundos_assistidos)->toBe(50);
});

it('registra salto de barra sem somar tempo ou concluir (RN-02)', function (): void {
    $cenario = cenarioProgresso();
    $progresso = ProgressoAula::factory()->for($cenario['matricula'])->for($cenario['aula'])->create([
        'posicao_maxima' => 10,
        'segundos_assistidos' => 10,
    ]);

    app(RegistrarProgresso::class)->executar($cenario['aluno'], $cenario['aula'], 95);

    $progresso->refresh();
    expect($progresso->posicao_maxima)->toBe(95)
        ->and($progresso->segundos_assistidos)->toBe(10)
        ->and($progresso->concluido_em)->toBeNull();
});

it('rejeita posicao maior que a duracao com tolerancia (RN-02)', function (): void {
    $cenario = cenarioProgresso();

    $this->actingAs($cenario['aluno'])->postJson(route('app.progresso'), [
        'aula_id' => $cenario['aula']->id,
        'posicao' => 106,
    ])->assertUnprocessable()->assertJsonValidationErrors('posicao');

    $this->actingAs($cenario['aluno'])->postJson(route('app.progresso'), [
        'aula_id' => $cenario['aula']->id,
        'posicao' => -1,
    ])->assertUnprocessable()->assertJsonValidationErrors('posicao');
});

it('rejeita ping sem matricula ativa (RN-02)', function (): void {
    $cenario = cenarioProgresso();
    $cenario['matricula']->update(['situacao' => SituacaoMatricula::Cancelada]);

    $this->actingAs($cenario['aluno'])->postJson(route('app.progresso'), [
        'aula_id' => $cenario['aula']->id,
        'posicao' => 10,
    ])->assertForbidden();
});
