<?php

declare(strict_types=1);

namespace App\Enums;

enum SituacaoUsuario: string
{
    case Pendente = 'pendente';
    case Ativo = 'ativo';
    case Bloqueado = 'bloqueado';

    public function rotulo(): string
    {
        return match ($this) {
            self::Pendente => 'Pendente',
            self::Ativo => 'Ativo',
            self::Bloqueado => 'Bloqueado',
        };
    }
}
