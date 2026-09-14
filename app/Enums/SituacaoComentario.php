<?php

declare(strict_types=1);

namespace App\Enums;

enum SituacaoComentario: string
{
    case Pendente = 'pendente';
    case Aprovado = 'aprovado';
    case Oculto = 'oculto';

    public function rotulo(): string
    {
        return match ($this) {
            self::Pendente => 'Pendente',
            self::Aprovado => 'Aprovado',
            self::Oculto => 'Oculto',
        };
    }
}
