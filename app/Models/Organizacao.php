<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TipoOrganizacao;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organizacao extends Model
{
    use HasFactory;

    protected $table = 'organizacoes';

    protected $fillable = ['nome', 'cnpj', 'tipo', 'uf', 'ativa'];

    protected function casts(): array
    {
        return ['tipo' => TipoOrganizacao::class, 'ativa' => 'boolean'];
    }

    public function usuarios(): HasMany
    {
        return $this->hasMany(Usuario::class);
    }
}
