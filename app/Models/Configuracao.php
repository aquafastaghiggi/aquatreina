<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Configuracao extends Model
{
    use HasFactory;

    protected $table = 'configuracoes';

    protected $fillable = ['chave', 'valor', 'tipo', 'descricao'];

    public static function valor(string $chave, mixed $padrao = null): mixed
    {
        return Cache::remember("configuracao.{$chave}", 300, function () use ($chave, $padrao): mixed {
            $configuracao = static::query()->where('chave', $chave)->first();

            if ($configuracao === null) {
                return $padrao;
            }

            return match ($configuracao->tipo) {
                'bool' => filter_var($configuracao->valor, FILTER_VALIDATE_BOOL),
                'int' => (int) $configuracao->valor,
                default => $configuracao->valor,
            };
        });
    }

    public static function definir(string $chave, mixed $valor): void
    {
        static::query()->updateOrCreate(['chave' => $chave], [
            'valor' => is_bool($valor) ? ($valor ? '1' : '0') : (string) $valor,
            'tipo' => is_bool($valor) ? 'bool' : (is_int($valor) ? 'int' : 'string'),
        ]);
        Cache::forget("configuracao.{$chave}");
    }
}
