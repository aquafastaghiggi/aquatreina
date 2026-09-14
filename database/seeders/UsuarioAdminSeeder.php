<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\SituacaoUsuario;
use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;
use Spatie\Permission\Models\Role;

class UsuarioAdminSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['aluno', 'instrutor', 'admin'] as $papel) {
            Role::findOrCreate($papel, 'web');
        }

        $email = env('ADMIN_EMAIL');
        $senha = env('ADMIN_PASSWORD');

        if (! is_string($email) || ! is_string($senha) || $email === '' || $senha === '') {
            throw new RuntimeException('Defina ADMIN_EMAIL e ADMIN_PASSWORD no .env antes de executar os seeders.');
        }

        $usuario = Usuario::query()->updateOrCreate(
            ['email' => $email],
            [
                'nome' => env('ADMIN_NOME', 'Administrador Aquafast'),
                'password' => Hash::make($senha),
                'situacao' => SituacaoUsuario::Ativo,
                'email_verified_at' => now(),
                'termos_aceitos_em' => now(),
                'termos_ip' => '127.0.0.1',
            ],
        );

        $usuario->syncRoles(['admin']);
    }
}
