<?php

declare(strict_types=1);

namespace App\Enums;

enum SituacaoMatricula: string
{
    case Ativa = 'ativa';
    case Concluida = 'concluida';
    case Cancelada = 'cancelada';

    public function rotulo(): string
    {
        return match ($this) {
            self::Ativa => 'Ativa',
            self::Concluida => 'Concluída',
            self::Cancelada => 'Cancelada',
        };
    }
}
