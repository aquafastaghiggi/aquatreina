<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ConfiguracaoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'chave' => fake()->unique()->slug(2),
            'valor' => fake()->word(),
            'tipo' => 'string',
            'descricao' => fake()->sentence(),
        ];
    }
}
