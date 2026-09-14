<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Material extends Model
{
    use HasFactory;

    protected $table = 'materiais';

    protected $fillable = [
        'aula_id', 'titulo', 'disco', 'caminho', 'mime', 'tamanho_bytes',
        'total_downloads', 'posicao',
    ];

    public function aula(): BelongsTo
    {
        return $this->belongsTo(Aula::class);
    }
}
