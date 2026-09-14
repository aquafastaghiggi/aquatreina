<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SituacaoComentario;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comentario extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'comentarios';

    protected $fillable = [
        'aula_id', 'usuario_id', 'comentario_pai_id', 'corpo', 'situacao',
        'e_resposta', 'fixado',
    ];

    protected function casts(): array
    {
        return [
            'situacao' => SituacaoComentario::class,
            'e_resposta' => 'boolean',
            'fixado' => 'boolean',
        ];
    }

    public function aula(): BelongsTo
    {
        return $this->belongsTo(Aula::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class);
    }

    public function comentarioPai(): BelongsTo
    {
        return $this->belongsTo(self::class, 'comentario_pai_id');
    }

    public function respostas(): HasMany
    {
        return $this->hasMany(self::class, 'comentario_pai_id')
            ->orderByDesc('e_resposta')
            ->orderBy('created_at');
    }

    public function scopeVisiveis(Builder $consulta, ?Usuario $usuario = null): Builder
    {
        return $consulta->where(function (Builder $filtro) use ($usuario): void {
            $filtro->where('situacao', SituacaoComentario::Aprovado);

            if ($usuario !== null) {
                $filtro->orWhere(function (Builder $proprios) use ($usuario): void {
                    $proprios
                        ->where('situacao', SituacaoComentario::Pendente)
                        ->where('usuario_id', $usuario->id);
                });
            }
        });
    }
}
