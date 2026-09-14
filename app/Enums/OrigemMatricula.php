<?php

declare(strict_types=1);

namespace App\Enums;

enum OrigemMatricula: string
{
    case Aluno = 'aluno';
    case Admin = 'admin';
    case Importacao = 'importacao';

    public function rotulo(): string
    {
        return match ($this) {
            self::Aluno => 'Aluno',
            self::Admin => 'Administração',
            self::Importacao => 'Importação',
        };
    }
}
