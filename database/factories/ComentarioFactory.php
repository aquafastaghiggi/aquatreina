<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\SituacaoComentario;
use App\Models\Aula;
use App\Models\Comentario;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Comentario> */
class ComentarioFactory extends Factory
{
    public function definition(): array
    {
        return [
            'aula_id' => Aula::factory(),
            'usuario_id' => Usuario::factory(),
            'comentario_pai_id' => null,
            'corpo' => fake()->paragraph(),
            'situacao' => SituacaoComentario::Aprovado,
            'e_resposta' => false,
            'fixado' => false,
        ];
    }
}
