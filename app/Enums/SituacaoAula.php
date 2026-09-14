<?php

declare(strict_types=1);

namespace App\Enums;

enum SituacaoAula: string
{
    case Rascunho = 'rascunho';
    case Publicada = 'publicada';

    public function rotulo(): string
    {
        return match ($this) {
            self::Rascunho => 'Rascunho',
            self::Publicada => 'Publicada',
        };
    }
}
