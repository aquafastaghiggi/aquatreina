<?php

declare(strict_types=1);

use App\Enums\SituacaoUsuario;
use App\Models\Usuario;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Contracts\Validation\UncompromisedVerifier;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    Role::findOrCreate('aluno');
    Role::findOrCreate('instrutor');
    Role::findOrCreate('admin');

    $this->mock(UncompromisedVerifier::class)
        ->shouldReceive('verify')
        ->andReturnTrue();
});

it('faz login com e-mail e senha válidos', function (): void {
    $usuario = Usuario::factory()->create(['password' => 'SenhaForte987']);

    $this->post(route('login.store'), [
        'email' => $usuario->email,
        'password' => 'SenhaForte987',
    ])->assertRedirect('/app');

    $this->assertAuthenticatedAs($usuario);
});

it('cadastro cria usuário aluno com situação pendente quando aprovação manual está ativa', function (): void {
    config()->set('treina.aprovacao_manual', true);

    $this->post(route('register.store'), [
        'nome' => 'Maria Distribuidora',
        'email' => 'maria@example.test',
        'telefone' => '11999999999',
        'empresa' => 'Distribuidora Sul',
        'cargo' => 'Vendas',
        'aceite_termos' => '1',
        'website' => '',
        'password' => 'SenhaForte987',
        'password_confirmation' => 'SenhaForte987',
    ])->assertRedirect('/app');

    $usuario = Usuario::query()->where('email', 'maria@example.test')->firstOrFail();

    expect($usuario->situacao)->toBe(SituacaoUsuario::Pendente)
        ->and($usuario->hasRole('aluno'))->toBeTrue();
});

it('cadastro grava aceite dos termos e IP', function (): void {
    $this->withServerVariables(['REMOTE_ADDR' => '2001:db8::10'])
        ->post(route('register.store'), [
            'nome' => 'João Cliente',
            'email' => 'joao@example.test',
            'aceite_termos' => '1',
            'website' => '',
            'password' => 'SenhaForte987',
            'password_confirmation' => 'SenhaForte987',
        ]);

    $usuario = Usuario::query()->where('email', 'joao@example.test')->firstOrFail();

    expect($usuario->termos_aceitos_em)->not->toBeNull()
        ->and($usuario->termos_ip)->toBe('2001:db8::10');
});

it('verifica o e-mail por link assinado', function (): void {
    Event::fake([Verified::class]);
    $usuario = Usuario::factory()->naoVerificado()->create();
    $url = URL::temporarySignedRoute('verification.verify', now()->addMinutes(10), [
        'id' => $usuario->id,
        'hash' => sha1($usuario->email),
    ]);

    $this->actingAs($usuario)->get($url)->assertRedirect('/app?verified=1');

    expect($usuario->fresh()->hasVerifiedEmail())->toBeTrue();
});

it('redefine a senha ponta a ponta', function (): void {
    Notification::fake();
    $usuario = Usuario::factory()->create();

    $this->post(route('password.email'), ['email' => $usuario->email])->assertSessionHas('status');

    Notification::assertSentTo($usuario, ResetPassword::class, function (ResetPassword $notificacao) use ($usuario): bool {
        $this->post(route('password.update'), [
            'token' => $notificacao->token,
            'email' => $usuario->email,
            'password' => 'NovaSenha987',
            'password_confirmation' => 'NovaSenha987',
        ])->assertRedirect(route('login'));

        return true;
    });

    expect(Hash::check('NovaSenha987', $usuario->fresh()->password))->toBeTrue();
});
