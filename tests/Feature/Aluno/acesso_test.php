<?php

declare(strict_types=1);

use App\Enums\SituacaoUsuario;
use App\Models\Usuario;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    Role::findOrCreate('aluno');
    Role::findOrCreate('admin');
});

it('usuário sem e-mail verificado não acessa a área do aluno', function (): void {
    $usuario = Usuario::factory()->naoVerificado()->create();

    $this->actingAs($usuario)->get('/app')->assertRedirect(route('verification.notice'));
});

it('usuário pendente cai em aguardando aprovação (RN-08)', function (): void {
    $usuario = Usuario::factory()->create(['situacao' => SituacaoUsuario::Pendente]);

    $this->actingAs($usuario)->get('/app')->assertRedirect(route('conta.pendente'));
});

it('usuário bloqueado é deslogado (RN-08)', function (): void {
    $usuario = Usuario::factory()->create(['situacao' => SituacaoUsuario::Bloqueado]);

    $this->actingAs($usuario)->get('/app')->assertRedirect(route('login'));

    $this->assertGuest();
});

it('último acesso não é atualizado duas vezes na mesma hora (RN-11)', function (): void {
    $usuario = Usuario::factory()->create(['ultimo_acesso_em' => null]);

    $this->actingAs($usuario)->get('/app')->assertOk();
    $primeiroAcesso = $usuario->fresh()->ultimo_acesso_em;

    $this->get('/app')->assertOk();

    expect($usuario->fresh()->ultimo_acesso_em->equalTo($primeiroAcesso))->toBeTrue();
});

it('aluno não acessa o painel administrativo', function (): void {
    $usuario = Usuario::factory()->create();
    $usuario->assignRole('aluno');

    $this->actingAs($usuario)->get('/admin')->assertForbidden();
});
