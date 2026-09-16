<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Usuario;

class UsuarioPolicy
{
    public function before(Usuario $usuario): ?bool
    {
        return $usuario->hasRole('admin') ? true : null;
    }

    public function viewAny(Usuario $usuario): bool
    {
        return $usuario->hasRole('admin');
    }

    public function view(Usuario $usuario, Usuario $alvo): bool
    {
        return $usuario->hasRole('admin');
    }

    public function aprovar(Usuario $usuario, Usuario $alvo): bool
    {
        return $usuario->hasRole('admin');
    }

    public function bloquear(Usuario $usuario, Usuario $alvo): bool
    {
        return $usuario->hasRole('admin');
    }

    public function anonimizar(Usuario $usuario, Usuario $alvo): bool
    {
        return $usuario->hasRole('admin');
    }
}
