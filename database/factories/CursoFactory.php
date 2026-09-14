<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\NivelCurso;
use App\Enums\SituacaoCurso;
use App\Models\Categoria;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CursoFactory extends Factory
{
    public function definition(): array
    {
        $titulo = fake()->unique()->sentence(4);

        return [
            'categoria_id' => Categoria::factory(),
            'responsavel_id' => Usuario::factory(),
            'titulo' => $titulo,
            'slug' => Str::slug($titulo).'-'.fake()->unique()->numberBetween(1, 99999),
            'subtitulo' => fake()->sentence(),
            'descricao' => fake()->paragraphs(2, true),
            'nivel' => NivelCurso::Basico,
            'situacao' => SituacaoCurso::Rascunho,
            'moderar_comentarios' => false,
            'liberacao_sequencial' => false,
            'posicao' => 0,
        ];
    }
}
