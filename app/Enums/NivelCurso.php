<?php

declare(strict_types=1);

namespace App\Enums;

enum NivelCurso: string
{
    case Basico = 'basico';
    case Intermediario = 'intermediario';
    case Avancado = 'avancado';

    public function rotulo(): string
    {
        return match ($this) {
            self::Basico => 'Básico',
            self::Intermediario => 'Intermediário',
            self::Avancado => 'Avançado',
        };
    }
}
