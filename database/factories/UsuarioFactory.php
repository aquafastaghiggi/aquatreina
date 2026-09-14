<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\SituacaoUsuario;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/** @extends Factory<Usuario> */
class UsuarioFactory extends Factory
{
    protected $model = Usuario::class;

    protected static ?string $password = null;

    public function definition(): array
    {
        return [
            'nome' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('senha-segura-para-testes'),
            'telefone' => fake()->optional()->numerify('###########'),
            'empresa' => fake()->optional()->company(),
            'cargo' => fake()->optional()->jobTitle(),
            'situacao' => SituacaoUsuario::Ativo,
            'termos_aceitos_em' => now(),
            'termos_ip' => '127.0.0.1',
            'remember_token' => Str::random(10),
        ];
    }

    public function naoVerificado(): static
    {
        return $this->state(fn (): array => ['email_verified_at' => null]);
    }
}
