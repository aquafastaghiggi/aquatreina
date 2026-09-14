<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TextoLegal extends Model
{
    protected $table = 'textos_legais';

    protected $fillable = ['tipo', 'versao', 'conteudo', 'publicado_em'];

    protected function casts(): array
    {
        return ['publicado_em' => 'datetime'];
    }

    public static function vigente(string $tipo): ?self
    {
        return static::query()->where('tipo', $tipo)->latest('versao')->first();
    }
}
