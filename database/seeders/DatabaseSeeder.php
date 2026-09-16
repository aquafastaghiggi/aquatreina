<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            TextoLegalSeeder::class,
            UsuarioAdminSeeder::class,
            CategoriaSeeder::class,
            ConfiguracaoSeeder::class,
            TrilhaProdutoSeeder::class,
        ]);

        if (app()->environment(['local', 'testing'])) {
            $this->call(CursoDemoSeeder::class);
        }
    }
}
