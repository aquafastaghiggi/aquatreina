<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Aula;
use Illuminate\Database\Eloquent\Factories\Factory;

class MaterialFactory extends Factory
{
    public function definition(): array
    {
        return [
            'aula_id' => Aula::factory(),
            'titulo' => fake()->sentence(3),
            'disco' => 'materiais',
            'caminho' => fake()->uuid().'.pdf',
            'mime' => 'application/pdf',
            'tamanho_bytes' => fake()->numberBetween(1024, 1000000),
            'total_downloads' => 0,
            'posicao' => 0,
        ];
    }
}
