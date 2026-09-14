<?php

declare(strict_types=1);

use App\Enums\SituacaoAula;
use App\Enums\SituacaoCurso;
use App\Enums\SituacaoMatricula;
use App\Models\Aula;
use App\Models\Curso;
use App\Models\Material;
use App\Models\Matricula;
use App\Models\Modulo;
use App\Models\Usuario;
use Illuminate\Support\Facades\Storage;

function cenarioMaterial(): array
{
    Storage::fake('materiais');
    $aluno = Usuario::factory()->create();
    $curso = Curso::factory()->create(['situacao' => SituacaoCurso::Publicado]);
    $modulo = Modulo::factory()->for($curso)->create();
    $aula = Aula::factory()->for($modulo)->create(['situacao' => SituacaoAula::Publicada]);
    $material = Material::factory()->for($aula)->create([
        'titulo' => 'Guia de aplicação',
        'caminho' => 'aulas/1/arquivo-aleatorio.pdf',
        'tamanho_bytes' => 7,
    ]);
    Storage::disk('materiais')->put($material->caminho, 'PDF-DEMO');

    return compact('aluno', 'curso', 'aula', 'material');
}

it('download sem matricula ativa retorna 403 (RN-07)', function (): void {
    $cenario = cenarioMaterial();
    Matricula::factory()->for($cenario['aluno'], 'usuario')->for($cenario['curso'])->create([
        'situacao' => SituacaoMatricula::Cancelada,
    ]);

    $this->actingAs($cenario['aluno'])
        ->get(route('app.materiais.baixar', $cenario['material']))
        ->assertForbidden();
});

it('download incrementa total e usa o titulo como nome (RN-07)', function (): void {
    $cenario = cenarioMaterial();
    Matricula::factory()->for($cenario['aluno'], 'usuario')->for($cenario['curso'])->create();

    $resposta = $this->actingAs($cenario['aluno'])
        ->get(route('app.materiais.baixar', $cenario['material']))
        ->assertOk();

    expect($resposta->headers->get('content-disposition'))
        ->toContain("filename*=utf-8''Guia%20de%20aplica%C3%A7%C3%A3o.pdf");

    expect($cenario['material']->fresh()->total_downloads)->toBe(1);
});

it('material nao fica acessivel pela url direta do storage (RN-07)', function (): void {
    $cenario = cenarioMaterial();

    $this->actingAs($cenario['aluno'])
        ->get('/storage/'.$cenario['material']->caminho)
        ->assertForbidden();
});
