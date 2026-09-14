<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SituacaoUsuario;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class Usuario extends Authenticatable implements FilamentUser, MustVerifyEmail
{
    use HasFactory;
    use HasRoles;
    use Notifiable;
    use SoftDeletes;

    protected $table = 'usuarios';

    protected $fillable = [
        'nome', 'email', 'password', 'telefone', 'empresa', 'cargo', 'organizacao_id',
        'situacao', 'avatar_caminho', 'ultimo_acesso_em', 'termos_aceitos_em', 'termos_ip',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'situacao' => SituacaoUsuario::class,
            'ultimo_acesso_em' => 'datetime',
            'termos_aceitos_em' => 'datetime',
        ];
    }

    public function organizacao(): BelongsTo
    {
        return $this->belongsTo(Organizacao::class);
    }

    public function cursosResponsavel(): HasMany
    {
        return $this->hasMany(Curso::class, 'responsavel_id');
    }

    public function matriculas(): HasMany
    {
        return $this->hasMany(Matricula::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $panel->getId() === 'admin' && $this->can('acessar-admin');
    }

    public function getNameAttribute(): string
    {
        return $this->nome;
    }
}
