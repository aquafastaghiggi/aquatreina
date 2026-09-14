<?php

declare(strict_types=1);

namespace App\Acoes\Usuario;

use App\Enums\SituacaoUsuario;
use App\Models\Usuario;

final class AprovarUsuario
{
    public function executar(Usuario $usuario, ?Usuario $responsavel = null): Usuario
    {
        $usuario->update(['situacao' => SituacaoUsuario::Ativo]);

        activity('usuarios')
            ->performedOn($usuario)
            ->causedBy($responsavel)
            ->withProperty('situacao', SituacaoUsuario::Ativo->value)
            ->log('usuario_aprovado');

        return $usuario->refresh();
    }
}
