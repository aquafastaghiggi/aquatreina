<?php

declare(strict_types=1);

namespace App\Excecoes;

use RuntimeException;

final class UsuarioPossuiHistorico extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Este aluno possui histórico na plataforma. Use a anonimização para preservar matrículas, progresso, comentários e relatórios.');
    }
}
