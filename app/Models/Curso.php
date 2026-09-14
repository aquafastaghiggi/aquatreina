<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\NivelCurso;
use App\Enums\SituacaoCurso;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Curso extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'cursos';

    protected $fillable = [
        'categoria_id', 'responsavel_id', 'titulo', 'slug', 'subtitulo', 'descricao',
        'capa_caminho', 'nivel', 'situacao', 'moderar_comentarios',
        'liberacao_sequencial', 'total_aulas', 'minutos_estimados', 'posicao', 'publicado_em',
    ];

    protected function casts(): array
    {
        return [
            'nivel' => NivelCurso::class,
            'situacao' => SituacaoCurso::class,
            'moderar_comentarios' => 'boolean',
            'liberacao_sequencial' => 'boolean',
            'publicado_em' => 'datetime',
        ];
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function responsavel(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'responsavel_id');
    }

    public function modulos(): HasMany
    {
        return $this->hasMany(Modulo::class)->orderBy('posicao');
    }

    public function matriculas(): HasMany
    {
        return $this->hasMany(Matricula::class);
    }

    public function scopePublicados(Builder $consulta): Builder
    {
        return $consulta->where('situacao', SituacaoCurso::Publicado);
    }
}
