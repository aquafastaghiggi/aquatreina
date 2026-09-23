<?php

declare(strict_types=1);

use App\Acoes\Usuario\AnonimizarUsuario;
use App\Consultas\Relatorios;
use App\Enums\SituacaoAula;
use App\Enums\SituacaoComentario;
use App\Enums\SituacaoCurso;
use App\Enums\SituacaoMatricula;
use App\Enums\SituacaoUsuario;
use App\Exportacoes\RelatorioExport;
use App\Models\Aula;
use App\Models\Comentario;
use App\Models\Configuracao;
use App\Models\Curso;
use App\Models\Matricula;
use App\Models\Modulo;
use App\Models\ProgressoAula;
use App\Models\Usuario;
use App\Servicos\Importacao\ImportadorUsuarios;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Spatie\Permission\Models\Role;

function cursoOperacional(string $slug = 'curso-operacional'): array
{
    $curso = Curso::factory()->create(['titulo' => 'Curso operacional', 'slug' => $slug, 'situacao' => SituacaoCurso::Publicado]);
    $modulo = Modulo::factory()->for($curso)->create();
    $aula = Aula::factory()->for($modulo)->create(['titulo' => 'Aula inicial', 'situacao' => SituacaoAula::Publicada]);

    return compact('curso', 'modulo', 'aula');
}

it('calcula os números do relatório por curso', function (): void {
    $cenario = cursoOperacional();
    foreach ([20, 80, 100] as $indice => $percentual) {
        Matricula::factory()->for($cenario['curso'])->create([
            'percentual_progresso' => $percentual,
            'situacao' => $indice === 2 ? SituacaoMatricula::Concluida : SituacaoMatricula::Ativa,
            'ultima_aula_id' => $cenario['aula']->id,
        ]);
    }

    $linha = app(Relatorios::class)->cursos()->firstOrFail();
    expect((int) $linha->matriculados)->toBe(3)
        ->and((int) $linha->concluidos)->toBe(1)
        ->and((float) $linha->percentual_medio)->toBe(66.7)
        ->and($linha->aula_maior_abandono)->toBe('Aula inicial');
});

it('exporta uma linha por curso no XLSX além do cabeçalho', function (): void {
    Curso::factory()->count(3)->create();
    $conteudo = Excel::raw(new RelatorioExport('cursos'), Maatwebsite\Excel\Excel::XLSX);
    $temporario = tempnam(sys_get_temp_dir(), 'treina-xlsx-');
    file_put_contents($temporario, $conteudo);
    $linhas = IOFactory::load($temporario)->getActiveSheet()->getHighestDataRow();
    unlink($temporario);

    expect($linhas)->toBe(4);
});

it('importa linhas válidas, reporta inválida e não duplica e-mail existente', function (): void {
    Mail::fake();
    Role::findOrCreate('aluno', 'web');
    cursoOperacional('curso-csv');
    Usuario::factory()->create(['email' => 'existente@example.com']);
    $csv = tempnam(sys_get_temp_dir(), 'treina-csv-');
    $linhas = ['nome,email,telefone,empresa,cargo,organizacao,cursos', 'Já Existe,existente@example.com,,,,,curso-csv'];
    foreach (range(1, 46) as $numero) {
        $linhas[] = "Pessoa {$numero},pessoa{$numero}@example.com,,,,,curso-csv";
    }
    array_push($linhas, 'Inválida 1,email-invalido,,,,,curso-csv', 'Inválida 2,,,,,,curso-csv', 'Inválida 3,invalida3@example.com,,,,,curso-inexistente');
    file_put_contents($csv, implode("\n", $linhas));

    $relatorio = app(ImportadorUsuarios::class)->importar($csv);
    unlink($csv);

    expect($relatorio['importados'])->toBe(47)->and($relatorio['falhas'])->toHaveCount(3)
        ->and(Usuario::query()->where('email', 'existente@example.com')->count())->toBe(1)
        ->and(Usuario::query()->where('email', 'pessoa1@example.com')->firstOrFail()->matriculas)->toHaveCount(1);
});

it('anonimiza dados e comentários sem apagar matrícula e progresso e registra atividade', function (): void {
    $cenario = cursoOperacional();
    $usuario = Usuario::factory()->create(['telefone' => '11999999999', 'empresa' => 'Empresa', 'cargo' => 'Cargo', 'avatar_caminho' => null]);
    $matricula = Matricula::factory()->for($usuario)->for($cenario['curso'])->create();
    ProgressoAula::factory()->for($matricula)->for($cenario['aula'])->create();
    $comentario = Comentario::factory()->for($usuario)->for($cenario['aula'])->create();

    app(AnonimizarUsuario::class)->executar($usuario);

    $usuario->refresh();
    expect($usuario->nome)->toBe('Usuario removido')->and($usuario->email)->toEndWith('@anonimizado.invalid')
        ->and($usuario->telefone)->toBeNull()->and($usuario->empresa)->toBeNull()->and($usuario->cargo)->toBeNull()
        ->and($usuario->situacao)->toBe(SituacaoUsuario::Bloqueado)
        ->and($matricula->fresh())->not->toBeNull()->and($matricula->progressos()->count())->toBe(1)
        ->and($comentario->fresh()->situacao)->toBe(SituacaoComentario::Oculto)
        ->and(DB::table('activity_log')->where('description', 'usuario_anonimizado')->exists())->toBeTrue();
});

it('cadastro permanece pendente mesmo com configuração legada desligada', function (): void {
    Role::findOrCreate('aluno', 'web');
    Configuracao::definir('aprovacao_manual', false);

    $this->post(route('register.store'), [
        'nome' => 'Cadastro Automático', 'email' => 'automatico@example.com', 'password' => 'SenhaForte123!',
        'password_confirmation' => 'SenhaForte123!', 'aceite_termos' => '1', 'website' => '',
    ])->assertRedirect();

    expect(Usuario::query()->where('email', 'automatico@example.com')->firstOrFail()->situacao)->toBe(SituacaoUsuario::Pendente);
});

it('comando de conferência corrige o progresso divergente', function (): void {
    $cenario = cursoOperacional();
    $matricula = Matricula::factory()->for($cenario['curso'])->create(['percentual_progresso' => 7]);
    ProgressoAula::factory()->for($matricula)->for($cenario['aula'])->create(['concluido_em' => now()]);

    $this->artisan('treina:conferir-progresso')->assertSuccessful();

    expect($matricula->fresh()->percentual_progresso)->toBe(100);
});

it('expõe a saúde da aplicação e do banco', function (): void {
    $this->get('/saude')->assertOk()->assertJson(['versao' => '1.0.0', 'banco' => 'ok']);
});

it('admin acessa relatórios configurações e importação', function (): void {
    Role::findOrCreate('admin', 'web');
    $admin = Usuario::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin)->get('/admin/relatorios')->assertOk();
    $this->actingAs($admin)->get('/admin/configuracoes')->assertOk();
    $this->actingAs($admin)->get('/admin/usuarios/importar')->assertOk();
});

it('relatório por aluno renderiza a situação do usuário sem erro', function (): void {
    Role::findOrCreate('admin', 'web');
    $admin = Usuario::factory()->create();
    $admin->assignRole('admin');
    Usuario::factory()->create(['situacao' => SituacaoUsuario::Ativo]);

    Livewire::actingAs($admin)
        ->test(App\Filament\Pages\Relatorios::class)
        ->call('mudarAba', 'alunos')
        ->assertOk()
        ->assertSee('Ativo');
});
