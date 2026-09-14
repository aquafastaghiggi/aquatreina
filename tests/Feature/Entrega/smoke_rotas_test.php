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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    foreach (['aluno', 'instrutor', 'admin'] as $papel) {
        Role::findOrCreate($papel, 'web');
    }
});

function cenarioSmokeEntrega(): array
{
    $curso = Curso::factory()->create(['situacao' => SituacaoCurso::Publicado, 'publicado_em' => now()]);
    $modulo = Modulo::factory()->for($curso)->create();
    $aula = Aula::factory()->for($modulo)->create([
        'situacao' => SituacaoAula::Publicada, 'amostra_gratuita' => true, 'publicada_em' => now(),
    ]);

    return compact('curso', 'modulo', 'aula');
}

it('faz smoke das rotas públicas', function (): void {
    $cenario = cenarioSmokeEntrega();
    $rotas = [
        route('vitrine'), route('cursos.mostrar', $cenario['curso']),
        route('cursos.amostra', [$cenario['curso'], $cenario['aula']]),
        route('termos'), route('privacidade'), route('saude'),
        route('login'), route('register'), route('password.request'),
    ];

    foreach ($rotas as $rota) {
        $this->get($rota)->assertOk();
    }
});

it('faz smoke de todas as rotas GET do aluno autenticado', function (): void {
    Storage::fake('materiais');
    $cenario = cenarioSmokeEntrega();
    $aluno = Usuario::factory()->create();
    $aluno->assignRole('aluno');
    $matricula = Matricula::factory()->for($aluno, 'usuario')->for($cenario['curso'])->create([
        'situacao' => SituacaoMatricula::Ativa,
    ]);
    $material = Material::factory()->for($cenario['aula'])->create(['caminho' => 'smoke/material.pdf']);
    Storage::disk('materiais')->put($material->caminho, 'arquivo de teste');

    $this->actingAs($aluno)->get(route('app.painel'))->assertOk();
    $this->actingAs($aluno)->get(route('app.catalogo'))->assertOk();
    $this->actingAs($aluno)->get(route('app.curso', $cenario['curso']))->assertRedirect();
    $this->actingAs($aluno)->get(route('app.aula', [$cenario['curso'], $cenario['aula']]))->assertOk();
    $this->actingAs($aluno)->get(route('app.materiais.baixar', $material))->assertOk();
    $this->actingAs($aluno)->get(route('app.perfil'))->assertOk();
    $this->actingAs($aluno)->get(route('app.dados.exportar'))->assertOk()->assertJsonPath('usuario.id', $aluno->id);
    $this->actingAs($aluno)->get(route('app.notificacoes'))->assertOk();

    expect($matricula->fresh())->not->toBeNull();
});

it('permite admin e nega aluno no painel administrativo', function (): void {
    $admin = Usuario::factory()->create();
    $admin->assignRole('admin');
    $aluno = Usuario::factory()->create();
    $aluno->assignRole('aluno');

    $this->actingAs($admin)->get('/admin')->assertOk();
    $this->actingAs($aluno)->get('/admin')->assertForbidden();
});

it('envia os cabeçalhos de segurança em respostas web', function (): void {
    $resposta = $this->get('/')->assertOk()
        ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
        ->assertHeader('X-Content-Type-Options', 'nosniff')
        ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');

    expect($resposta->headers->get('Content-Security-Policy'))
        ->toContain('frame-src https://www.youtube-nocookie.com')
        ->toContain('img-src')
        ->toContain('https://i.ytimg.com');

    if (app()->isProduction() || config('seguranca.forcar_https')) {
        $resposta->assertHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        expect(config('session.secure'))->toBeTrue()
            ->and(config('session.http_only'))->toBeTrue()
            ->and(config('session.same_site'))->toBe('lax');
    }
});

it('não faz carregamento tardio nas telas principais do aluno', function (): void {
    $cenario = cenarioSmokeEntrega();
    Aula::factory()->count(2)->for($cenario['modulo'])->create(['situacao' => SituacaoAula::Publicada]);
    $aluno = Usuario::factory()->create();
    Matricula::factory()->for($aluno, 'usuario')->for($cenario['curso'])->create();
    Model::preventLazyLoading();

    try {
        $this->actingAs($aluno)->get(route('app.painel'))->assertOk();
        $this->actingAs($aluno)->get(route('app.catalogo'))->assertOk();
        $this->actingAs($aluno)->get(route('app.aula', [$cenario['curso'], $cenario['aula']]))->assertOk();
    } finally {
        Model::preventLazyLoading(false);
    }
});
