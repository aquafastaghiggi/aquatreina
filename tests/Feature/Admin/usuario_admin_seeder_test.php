<?php

declare(strict_types=1);

use App\Models\TextoLegal;
use App\Models\Usuario;
use Database\Seeders\TextoLegalSeeder;
use Database\Seeders\UsuarioAdminSeeder;

it('admin seedado ja nasce com os termos vigentes aceitos', function (): void {
    $_ENV['ADMIN_EMAIL'] = $_SERVER['ADMIN_EMAIL'] = 'admin-teste@aquafast.com.br';
    $_ENV['ADMIN_PASSWORD'] = $_SERVER['ADMIN_PASSWORD'] = 'senha-segura-123';

    $this->seed(TextoLegalSeeder::class);
    $this->seed(UsuarioAdminSeeder::class);

    $admin = Usuario::query()->where('email', 'admin-teste@aquafast.com.br')->firstOrFail();

    expect($admin->termos_versao_aceita)->toBe(TextoLegal::vigente('termos')->versao)
        ->and($admin->termos_versao_aceita)->not->toBeNull();

    unset($_ENV['ADMIN_EMAIL'], $_SERVER['ADMIN_EMAIL'], $_ENV['ADMIN_PASSWORD'], $_SERVER['ADMIN_PASSWORD']);
});
