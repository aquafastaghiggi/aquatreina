<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Notifications\Channels\MicrosoftGraphChannel;
use App\Servicos\Email\MensagemMicrosoftGraph;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

final class CadastroRecebido extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    /** @var list<int> */
    public array $backoff = [60, 300, 900];

    public function __construct(private readonly string $nome)
    {
        $this->onQueue('emails');
    }

    public function via(object $notifiable): array
    {
        return [MicrosoftGraphChannel::class];
    }

    public function toMicrosoftGraph(object $notifiable): MensagemMicrosoftGraph
    {
        return new MensagemMicrosoftGraph(
            assunto: 'Recebemos seu cadastro na Universidade Aquafast',
            titulo: 'Cadastro recebido',
            saudacao: "Olá, {$this->nome}!",
            linhas: [
                'Recebemos seu cadastro na Universidade Aquafast.',
                'Sua conta está aguardando a análise da nossa equipe. Assim que o cadastro for aprovado, você receberá um novo e-mail com o acesso liberado.',
            ],
            acaoTexto: 'Acessar a Universidade',
            acaoUrl: route('login'),
        );
    }
}
