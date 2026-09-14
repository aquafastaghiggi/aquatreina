<?php

declare(strict_types=1);

namespace App\Acoes\Usuario;

use App\Enums\SituacaoComentario;
use App\Enums\SituacaoUsuario;
use App\Models\Usuario;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Facades\Storage;

final class AnonimizarUsuario
{
    public function __construct(private readonly ConnectionInterface $banco) {}

    public function executar(Usuario $usuario, ?Usuario $responsavel = null): Usuario
    {
        $avatar = $usuario->avatar_caminho;
        $hash = hash('sha256', mb_strtolower($usuario->email).':'.$usuario->id);

        $this->banco->transaction(function () use ($usuario, $responsavel, $hash): void {
            $usuario->comentarios()->update(['situacao' => SituacaoComentario::Oculto]);
            $usuario->update([
                'nome' => 'Usuario removido', 'email' => $hash.'@anonimizado.invalid',
                'telefone' => null, 'empresa' => null, 'cargo' => null,
                'avatar_caminho' => null, 'situacao' => SituacaoUsuario::Bloqueado,
                'remember_token' => null,
            ]);
            activity('usuarios')->performedOn($usuario)->causedBy($responsavel)
                ->withProperty('usuario_id', $usuario->id)->log('usuario_anonimizado');
        });

        if ($avatar !== null) {
            Storage::disk('local')->delete($avatar);
        }

        return $usuario->refresh();
    }
}
