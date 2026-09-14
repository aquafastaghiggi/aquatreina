<?php

declare(strict_types=1);

namespace App\Enums;

enum ProvedorVideo: string
{
    case Youtube = 'youtube';
    case Bunny = 'bunny';
    case Externo = 'externo';

    public function rotulo(): string
    {
        return match ($this) {
            self::Youtube => 'YouTube',
            self::Bunny => 'Bunny Stream',
            self::Externo => 'Externo',
        };
    }
}
