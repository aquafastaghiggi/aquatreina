<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ProvedorVideo;
use App\Enums\SituacaoAula;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Aula extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'aulas';

    protected $fillable = [
        'modulo_id', 'titulo', 'slug', 'descricao', 'provedor', 'video_id',
        'duracao_segundos', 'amostra_gratuita', 'situacao', 'posicao', 'publicada_em',
    ];

    protected function casts(): array
    {
        return [
            'provedor' => ProvedorVideo::class,
            'situacao' => SituacaoAula::class,
            'amostra_gratuita' => 'boolean',
            'publicada_em' => 'datetime',
        ];
    }

    public function modulo(): BelongsTo
    {
        return $this->belongsTo(Modulo::class);
    }

    public function materiais(): HasMany
    {
        return $this->hasMany(Material::class)->orderBy('posicao');
    }

    public function scopePublicadas(Builder $consulta): Builder
    {
        return $consulta->where('situacao', SituacaoAula::Publicada);
    }
}
