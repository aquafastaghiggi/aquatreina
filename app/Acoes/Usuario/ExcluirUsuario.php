<?php

declare(strict_types=1);

namespace App\Acoes\Usuario;

use App\Excecoes\UsuarioPossuiHistorico;
use App\Models\Usuario;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;

final class ExcluirUsuario
{
    public function __construct(private readonly ConnectionInterface $banco) {}

    public function executar(Usuario $usuario, ?Usuario $responsavel = null): void
    {
        if (! $usuario->hasRole('aluno')) {
            throw new InvalidArgumentException('Somente contas de aluno podem ser excluídas por esta ação.');
        }

        if ($responsavel?->is($usuario)) {
            throw new InvalidArgumentException('O administrador não pode excluir a própria conta.');
        }

        if ($this->possuiHistorico($usuario)) {
            throw new UsuarioPossuiHistorico;
        }

        $avatar = $usuario->avatar_caminho;
        $usuarioId = $usuario->id;

        $this->banco->transaction(function () use ($usuario, $responsavel, $usuarioId): void {
            $usuario->notifications()->delete();
            $this->banco->table('sessions')->where('user_id', $usuarioId)->delete();
            $usuario->syncPermissions([]);
            $usuario->syncRoles([]);
            $usuario->forceDelete();

            activity('usuarios')
                ->causedBy($responsavel)
                ->withProperty('usuario_id', $usuarioId)
                ->log('usuario_excluido');
        });

        if ($avatar !== null) {
            Storage::disk('local')->delete($avatar);
        }
    }

    private function possuiHistorico(Usuario $usuario): bool
    {
        return $usuario->matriculas()->exists()
            || $usuario->comentarios()->withTrashed()->exists()
            || $usuario->cursosResponsavel()->withTrashed()->exists();
    }
}
