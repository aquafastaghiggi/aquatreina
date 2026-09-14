<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Aula;
use App\Models\Matricula;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProgressoAulaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'matricula_id' => Matricula::factory(),
            'aula_id' => Aula::factory(),
            'segundos_assistidos' => 0,
            'posicao_maxima' => 0,
            'primeira_visualizacao_em' => now(),
        ];
    }
}
