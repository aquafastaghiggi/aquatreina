<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\OrigemMatricula;
use App\Enums\SituacaoMatricula;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Matricula extends Model
{
    use HasFactory;

    protected $table = 'matriculas';

    protected $fillable = [
        'usuario_id', 'curso_id', 'origem', 'situacao', 'percentual_progresso',
        'ultima_aula_id', 'matriculado_em', 'concluido_em',
    ];

    protected function casts(): array
    {
        return [
            'origem' => OrigemMatricula::class,
            'situacao' => SituacaoMatricula::class,
            'matriculado_em' => 'datetime',
            'concluido_em' => 'datetime',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class);
    }

    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class);
    }

    public function ultimaAula(): BelongsTo
    {
        return $this->belongsTo(Aula::class, 'ultima_aula_id');
    }
}
