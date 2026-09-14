<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Produtos', 'Operação', 'Comercial', 'Qualidade'] as $posicao => $nome) {
            Categoria::query()->updateOrCreate(
                ['slug' => Str::slug($nome)],
                ['nome' => $nome, 'posicao' => $posicao],
            );
        }
    }
}
