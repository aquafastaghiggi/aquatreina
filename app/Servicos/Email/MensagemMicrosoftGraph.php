<?php

declare(strict_types=1);

namespace App\Servicos\Email;

final readonly class MensagemMicrosoftGraph
{
    /**
     * @param  list<string>  $linhas
     */
    public function __construct(
        public string $assunto,
        public string $titulo,
        public string $saudacao,
        public array $linhas,
        public ?string $acaoTexto = null,
        public ?string $acaoUrl = null,
    ) {}
}
