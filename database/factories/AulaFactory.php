<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ProvedorVideo;
use App\Enums\SituacaoAula;
use App\Models\Modulo;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AulaFactory extends Factory
{
    public function definition(): array
    {
        $titulo = fake()->sentence(4);

        return [
            'modulo_id' => Modulo::factory(),
            'titulo' => $titulo,
            'slug' => Str::slug($titulo).'-'.fake()->unique()->numberBetween(1, 99999),
            'descricao' => fake()->paragraph(),
            'provedor' => ProvedorVideo::Youtube,
            'video_id' => 'dQw4w9WgXcQ',
            'duracao_segundos' => 180,
            'amostra_gratuita' => false,
            'situacao' => SituacaoAula::Rascunho,
            'posicao' => 0,
        ];
    }
}
