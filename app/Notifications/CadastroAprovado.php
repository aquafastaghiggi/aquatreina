<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Notifications\Channels\MicrosoftGraphChannel;
use App\Servicos\Email\MensagemMicrosoftGraph;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

final class CadastroAprovado extends Notification implements ShouldQueue
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
            assunto: 'Seu cadastro foi aprovado na Universidade Aquafast',
            titulo: 'Acesso liberado',
            saudacao: "Olá, {$this->nome}!",
            linhas: [
                'Seu cadastro foi aprovado e sua área de treinamento já está liberada.',
                'Agora você pode acessar a Universidade Aquafast, conhecer os produtos e iniciar sua jornada de conteúdos e treinamentos.',
            ],
            acaoTexto: 'Entrar na Universidade',
            acaoUrl: route('login'),
        );
    }
}
