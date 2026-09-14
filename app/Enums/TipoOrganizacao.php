<?php

declare(strict_types=1);

namespace App\Enums;

enum TipoOrganizacao: string
{
    case Distribuidor = 'distribuidor';
    case Representante = 'representante';
    case Cliente = 'cliente';

    public function rotulo(): string
    {
        return match ($this) {
            self::Distribuidor => 'Distribuidor',
            self::Representante => 'Representante',
            self::Cliente => 'Cliente',
        };
    }
}
