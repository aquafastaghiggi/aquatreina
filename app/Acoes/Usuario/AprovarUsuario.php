<?php

declare(strict_types=1);

namespace App\Acoes\Usuario;

use App\Enums\SituacaoUsuario;
use App\Models\Usuario;
use App\Notifications\CadastroAprovado;

final class AprovarUsuario
{
    public function executar(Usuario $usuario, ?Usuario $responsavel = null): Usuario
    {
        $jaEstavaAtivo = $usuario->situacao === SituacaoUsuario::Ativo;

        $usuario->update(['situacao' => SituacaoUsuario::Ativo]);

        activity('usuarios')
            ->performedOn($usuario)
            ->causedBy($responsavel)
            ->withProperty('situacao', SituacaoUsuario::Ativo->value)
            ->log('usuario_aprovado');

        $usuario = $usuario->refresh();

        if (! $jaEstavaAtivo) {
            $usuario->notify(new CadastroAprovado($usuario->nome));
        }

        return $usuario;
    }
}
