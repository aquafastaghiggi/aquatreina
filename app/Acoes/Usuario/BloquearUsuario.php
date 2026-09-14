<?php

declare(strict_types=1);

namespace App\Acoes\Usuario;

use App\Enums\SituacaoUsuario;
use App\Models\Usuario;

final class BloquearUsuario
{
    public function executar(Usuario $usuario, ?Usuario $responsavel = null): Usuario
    {
        $usuario->update(['situacao' => SituacaoUsuario::Bloqueado]);

        activity('usuarios')
            ->performedOn($usuario)
            ->causedBy($responsavel)
            ->withProperty('situacao', SituacaoUsuario::Bloqueado->value)
            ->log('usuario_bloqueado');

        return $usuario->refresh();
    }
}
