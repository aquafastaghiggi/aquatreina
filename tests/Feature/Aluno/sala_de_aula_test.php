<?php

declare(strict_types=1);

use App\Enums\SituacaoAula;
use App\Enums\SituacaoCurso;
use App\Models\Aula;
use App\Models\Curso;
use App\Models\Matricula;
use App\Models\Modulo;
use App\Models\ProgressoAula;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;

it('aula em rascunho retorna 404 pela url direta (RN-03)', function (): void {
    $aluno = Usuario::factory()->create();
    $curso = Curso::factory()->create(['situacao' => SituacaoCurso::Publicado]);
    $modulo = Modulo::factory()->for($curso)->create();
    $aula = Aula::factory()->for($modulo)->create(['situacao' => SituacaoAula::Rascunho]);
    Matricula::factory()->for($aluno, 'usuario')->for($curso)->create();

    $this->actingAs($aluno)->get(route('app.aula', [$curso, $aula]))->assertNotFound();
});

it('aula de outro curso sem matricula retorna 403 (RN-03)', function (): void {
    $aluno = Usuario::factory()->create();
    $cursoMatriculado = Curso::factory()->create(['situacao' => SituacaoCurso::Publicado]);
    $outroCurso = Curso::factory()->create(['situacao' => SituacaoCurso::Publicado]);
    $outroModulo = Modulo::factory()->for($outroCurso)->create();
    $outraAula = Aula::factory()->for($outroModulo)->create(['situacao' => SituacaoAula::Publicada]);
    Matricula::factory()->for($aluno, 'usuario')->for($cursoMatriculado)->create();

    $this->actingAs($aluno)->get(route('app.aula', [$cursoMatriculado, $outraAula]))->assertForbidden();
});

it('nao refaz a consulta de aula e matricula entre sala de aula e aba de comentarios', function (): void {
    $aluno = Usuario::factory()->create();
    $curso = Curso::factory()->create(['situacao' => SituacaoCurso::Publicado]);
    $modulo = Modulo::factory()->for($curso)->create();
    $aula = Aula::factory()->for($modulo)->create(['situacao' => SituacaoAula::Publicada]);
    Matricula::factory()->for($aluno, 'usuario')->for($curso)->create();

    $consultasAula = 0;
    $consultasExistsMatricula = 0;
    DB::listen(function ($consulta) use (&$consultasAula, &$consultasExistsMatricula): void {
        if (str_contains($consulta->sql, 'from "aulas" where "aulas"."id" =')) {
            $consultasAula++;
        }
        if (str_contains($consulta->sql, 'select exists(select * from "matriculas"')) {
            $consultasExistsMatricula++;
        }
    });

    $this->actingAs($aluno)->get(route('app.aula', [$curso, $aula]))->assertOk();

    expect($consultasAula)->toBeLessThanOrEqual(2)
        ->and($consultasExistsMatricula)->toBeLessThanOrEqual(1);
});

it('entrada do curso redireciona para a proxima aula nao concluida', function (): void {
    $aluno = Usuario::factory()->create();
    $curso = Curso::factory()->create(['situacao' => SituacaoCurso::Publicado]);
    $modulo = Modulo::factory()->for($curso)->create();
    $primeira = Aula::factory()->for($modulo)->create(['situacao' => SituacaoAula::Publicada, 'posicao' => 0]);
    $segunda = Aula::factory()->for($modulo)->create(['situacao' => SituacaoAula::Publicada, 'posicao' => 1]);
    $matricula = Matricula::factory()->for($aluno, 'usuario')->for($curso)->create();
    ProgressoAula::factory()->for($matricula)->for($primeira)->create(['concluido_em' => now()]);

    $this->actingAs($aluno)->get(route('app.curso', $curso))
        ->assertRedirect(route('app.aula', [$curso, $segunda]));
});
