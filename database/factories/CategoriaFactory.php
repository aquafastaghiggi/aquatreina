<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoriaFactory extends Factory
{
    public function definition(): array
    {
        $nome = fake()->unique()->words(2, true);

        return [
            'nome' => Str::title($nome),
            'slug' => Str::slug($nome).'-'.fake()->unique()->numberBetween(1, 99999),
            'cor' => fake()->hexColor(),
            'posicao' => fake()->numberBetween(0, 20),
        ];
    }
}
