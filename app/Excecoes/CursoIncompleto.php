<?php

declare(strict_types=1);

namespace App\Excecoes;

use DomainException;

final class CursoIncompleto extends DomainException
{
    /** @param list<string> $pendencias */
    public function __construct(public readonly array $pendencias)
    {
        parent::__construct('O curso ainda possui pendências para publicação.');
    }
}
