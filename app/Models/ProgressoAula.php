<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgressoAula extends Model
{
    use HasFactory;

    protected $table = 'progresso_aulas';

    protected $fillable = [
        'matricula_id', 'aula_id', 'segundos_assistidos', 'posicao_maxima',
        'primeira_visualizacao_em', 'concluido_em',
    ];

    protected function casts(): array
    {
        return [
            'primeira_visualizacao_em' => 'datetime',
            'concluido_em' => 'datetime',
        ];
    }

    public function matricula(): BelongsTo
    {
        return $this->belongsTo(Matricula::class);
    }

    public function aula(): BelongsTo
    {
        return $this->belongsTo(Aula::class);
    }
}
