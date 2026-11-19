<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Notifications\Channels\MicrosoftGraphChannel;
use App\Servicos\Email\MensagemMicrosoftGraph;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

final class NovoCadastroNaUniversidade extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    /** @var list<int> */
    public array $backoff = [60, 300, 900];

    public function __construct(
        private readonly string $nome,
        private readonly string $email,
        private readonly ?string $telefone,
    ) {
        $this->onQueue('emails');
    }

    public function via(object $notifiable): array
    {
        return [MicrosoftGraphChannel::class];
    }

    public function toMicrosoftGraph(object $notifiable): MensagemMicrosoftGraph
    {
        $telefone = $this->telefone ?: 'Não informado';

        return new MensagemMicrosoftGraph(
            assunto: 'Novo cadastro aguardando aprovação',
            titulo: 'Novo cadastro na Universidade',
            saudacao: 'Olá, equipe Aquafast!',
            linhas: [
                'Um novo cadastro está aguardando aprovação na Universidade Aquafast.',
                "Nome: {$this->nome}",
                "E-mail: {$this->email}",
                "Telefone: {$telefone}",
            ],
            acaoTexto: 'Revisar cadastro',
            acaoUrl: url('/admin/usuarios'),
        );
    }
}
