<?php

declare(strict_types=1);

use App\Acoes\Comentario\CriarComentario;
use App\Acoes\Comentario\ModerarComentario;
use App\Acoes\Comentario\ResponderComentario;
use App\Enums\SituacaoAula;
use App\Enums\SituacaoComentario;
use App\Enums\SituacaoCurso;
use App\Eventos\AulaPublicada;
use App\Livewire\Aluno\Notificacoes;
use App\Models\Aula;
use App\Models\Comentario;
use App\Models\Curso;
use App\Models\Matricula;
use App\Models\Modulo;
use App\Models\Usuario;
use App\Notifications\NovoConteudoNoCurso;
use App\Notifications\RespostaRecebida;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

function cenarioComentario(bool $moderar = false): array
{
    Role::findOrCreate('aluno', 'web');
    Role::findOrCreate('instrutor', 'web');
    $instrutor = Usuario::factory()->create();
    $instrutor->assignRole('instrutor');
    $aluno = Usuario::factory()->create();
    $aluno->assignRole('aluno');
    $curso = Curso::factory()->for($instrutor, 'responsavel')->create([
        'situacao' => SituacaoCurso::Publicado,
        'moderar_comentarios' => $moderar,
    ]);
    $modulo = Modulo::factory()->for($curso)->create();
    $aula = Aula::factory()->for($modulo)->create(['situacao' => SituacaoAula::Publicada]);
    Matricula::factory()->for($aluno, 'usuario')->for($curso)->create();

    return compact('instrutor', 'aluno', 'curso', 'aula');
}

it('comentario nasce aprovado em curso normal (RN-06)', function (): void {
    $cenario = cenarioComentario();

    $comentario = app(CriarComentario::class)->executar(
        $cenario['aluno'],
        $cenario['aula'],
        'Como aplicar este produto?',
    );

    expect($comentario->situacao)->toBe(SituacaoComentario::Aprovado);
});

it('comentario nasce pendente quando o curso exige moderacao (RN-06)', function (): void {
    $cenario = cenarioComentario(true);

    $comentario = app(CriarComentario::class)->executar(
        $cenario['aluno'],
        $cenario['aula'],
        'Esta pergunta precisa de análise.',
    );

    expect($comentario->situacao)->toBe(SituacaoComentario::Pendente);
});

it('comentario pendente e visivel apenas para o autor (RN-06)', function (): void {
    $cenario = cenarioComentario(true);
    $outroAluno = Usuario::factory()->create();
    $outroAluno->assignRole('aluno');
    Matricula::factory()->for($outroAluno, 'usuario')->for($cenario['curso'])->create();
    $texto = 'Pergunta aguardando moderação';
    app(CriarComentario::class)->executar($cenario['aluno'], $cenario['aula'], $texto);

    $this->actingAs($cenario['aluno'])
        ->get(route('app.aula', [$cenario['curso'], $cenario['aula']]))
        ->assertOk()
        ->assertSee($texto)
        ->assertSee('Em análise');

    $this->actingAs($outroAluno)
        ->get(route('app.aula', [$cenario['curso'], $cenario['aula']]))
        ->assertOk()
        ->assertDontSee($texto);
});

it('aluno sem matricula ativa nao comenta (RN-06)', function (): void {
    $cenario = cenarioComentario();
    $cenario['curso']->matriculas()->where('usuario_id', $cenario['aluno']->id)->delete();

    expect(fn () => app(CriarComentario::class)->executar(
        $cenario['aluno'],
        $cenario['aula'],
        'Pergunta sem matrícula.',
    ))->toThrow(AuthorizationException::class);
});

it('throttle corta o decimo primeiro comentario no mesmo minuto (RN-06)', function (): void {
    $cenario = cenarioComentario();
    $criar = app(CriarComentario::class);

    foreach (range(1, 10) as $numero) {
        $criar->executar($cenario['aluno'], $cenario['aula'], "Pergunta número {$numero}");
    }

    expect(fn () => $criar->executar(
        $cenario['aluno'],
        $cenario['aula'],
        'Décima primeira pergunta.',
    ))->toThrow(TooManyRequestsHttpException::class);
});

it('resposta do instrutor notifica o autor por app e email (RN-06)', function (): void {
    Notification::fake();
    $cenario = cenarioComentario();
    $pergunta = Comentario::factory()->for($cenario['aula'])->for($cenario['aluno'], 'usuario')->create();

    app(ResponderComentario::class)->executar(
        $cenario['instrutor'],
        $pergunta,
        'Esta é a orientação oficial.',
    );

    Notification::assertSentTo(
        $cenario['aluno'],
        RespostaRecebida::class,
        fn (RespostaRecebida $notificacao, array $canais): bool => in_array('database', $canais, true)
            && in_array('mail', $canais, true),
    );
});

it('instrutor nao modera comentario de curso alheio (RN-06)', function (): void {
    $cenario = cenarioComentario();
    $outroInstrutor = Usuario::factory()->create();
    $outroInstrutor->assignRole('instrutor');
    $pergunta = Comentario::factory()->for($cenario['aula'])->for($cenario['aluno'], 'usuario')->create();

    expect(fn () => app(ModerarComentario::class)->executar(
        $outroInstrutor,
        $pergunta,
        SituacaoComentario::Oculto,
    ))->toThrow(AuthorizationException::class);
});

it('publicar aula notifica alunos matriculados no curso', function (): void {
    Notification::fake();
    $cenario = cenarioComentario();

    AulaPublicada::dispatch($cenario['aula']);

    Notification::assertSentTo($cenario['aluno'], NovoConteudoNoCurso::class);
});

it('aluno consulta e marca notificacao como lida', function (): void {
    $cenario = cenarioComentario();
    $pergunta = Comentario::factory()->for($cenario['aula'])->for($cenario['aluno'], 'usuario')->create();
    $resposta = Comentario::factory()->for($cenario['aula'])->for($cenario['instrutor'], 'usuario')->create([
        'comentario_pai_id' => $pergunta->id,
        'e_resposta' => true,
    ]);
    $cenario['aluno']->notifyNow(new RespostaRecebida($resposta->id), ['database']);
    $id = $cenario['aluno']->unreadNotifications()->firstOrFail()->id;

    $this->actingAs($cenario['aluno'])
        ->get(route('app.notificacoes'))
        ->assertOk()
        ->assertSee('Sua pergunta foi respondida');

    Livewire::actingAs($cenario['aluno'])
        ->test(Notificacoes::class)
        ->call('marcarComoLida', $id)
        ->assertHasNoErrors();

    expect($cenario['aluno']->unreadNotifications()->count())->toBe(0);
});
