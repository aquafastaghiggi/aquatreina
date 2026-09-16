<?php

declare(strict_types=1);

use App\Acoes\Usuario\AprovarUsuario;
use App\Acoes\Usuario\BloquearUsuario;
use App\Enums\SituacaoUsuario;
use App\Filament\Resources\Usuarios\UsuarioResource;
use App\Models\Usuario;
use Illuminate\Support\Facades\Gate;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    Role::findOrCreate('admin');
    Role::findOrCreate('instrutor');
});

it('libera o painel administrativo para admin', function (): void {
    $admin = Usuario::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin)->get('/admin')->assertOk();
});

it('nega o painel administrativo para admin bloqueado (RN-08)', function (): void {
    $admin = Usuario::factory()->create(['situacao' => SituacaoUsuario::Bloqueado]);
    $admin->assignRole('admin');

    $this->actingAs($admin)->get('/admin')->assertForbidden();
});

it('nega o painel administrativo para admin pendente (RN-08)', function (): void {
    $admin = Usuario::factory()->create(['situacao' => SituacaoUsuario::Pendente]);
    $admin->assignRole('admin');

    $this->actingAs($admin)->get('/admin')->assertForbidden();
});

it('aprova e bloqueia usuário registrando as ações no activity log', function (): void {
    $admin = Usuario::factory()->create();
    $admin->assignRole('admin');
    $usuario = Usuario::factory()->create(['situacao' => SituacaoUsuario::Pendente]);

    app(AprovarUsuario::class)->executar($usuario, $admin);
    expect($usuario->fresh()->situacao)->toBe(SituacaoUsuario::Ativo);

    app(BloquearUsuario::class)->executar($usuario, $admin);
    expect($usuario->fresh()->situacao)->toBe(SituacaoUsuario::Bloqueado)
        ->and(Activity::query()->where('subject_id', $usuario->id)->count())->toBe(2);
});

it('mostra no menu a contagem de usuarios pendentes de aprovacao', function (): void {
    $admin = Usuario::factory()->create();
    $admin->assignRole('admin');
    Usuario::factory()->create(['situacao' => SituacaoUsuario::Pendente]);
    Usuario::factory()->create(['situacao' => SituacaoUsuario::Pendente]);
    Usuario::factory()->create(['situacao' => SituacaoUsuario::Ativo]);

    $this->actingAs($admin);

    expect(UsuarioResource::getNavigationBadge())->toBe('2');
});

it('so admin pode aprovar bloquear e anonimizar usuario (UsuarioPolicy)', function (): void {
    $admin = Usuario::factory()->create();
    $admin->assignRole('admin');
    $instrutor = Usuario::factory()->create();
    $instrutor->assignRole('instrutor');
    $usuario = Usuario::factory()->create();

    expect(Gate::forUser($admin)->allows('aprovar', $usuario))->toBeTrue()
        ->and(Gate::forUser($admin)->allows('bloquear', $usuario))->toBeTrue()
        ->and(Gate::forUser($admin)->allows('anonimizar', $usuario))->toBeTrue()
        ->and(Gate::forUser($instrutor)->allows('aprovar', $usuario))->toBeFalse()
        ->and(Gate::forUser($instrutor)->allows('bloquear', $usuario))->toBeFalse()
        ->and(Gate::forUser($instrutor)->allows('anonimizar', $usuario))->toBeFalse();
});
