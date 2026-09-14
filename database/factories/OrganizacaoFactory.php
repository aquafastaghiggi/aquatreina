<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\TipoOrganizacao;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrganizacaoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nome' => fake()->company(),
            'cnpj' => null,
            'tipo' => TipoOrganizacao::Distribuidor,
            'uf' => fake()->stateAbbr(),
            'ativa' => true,
        ];
    }
}
