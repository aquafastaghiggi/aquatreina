<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\OrigemMatricula;
use App\Enums\SituacaoMatricula;
use App\Models\Curso;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

class MatriculaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'usuario_id' => Usuario::factory(),
            'curso_id' => Curso::factory(),
            'origem' => OrigemMatricula::Aluno,
            'situacao' => SituacaoMatricula::Ativa,
            'percentual_progresso' => 0,
            'matriculado_em' => now(),
        ];
    }
}
