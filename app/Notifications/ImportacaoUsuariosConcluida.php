<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class ImportacaoUsuariosConcluida extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly array $relatorio) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)->subject('Importação de usuários concluída')
            ->line("Importados: {$this->relatorio['importados']}")
            ->line('Falhas: '.count($this->relatorio['falhas']));
    }

    public function toArray(object $notifiable): array
    {
        return $this->relatorio;
    }
}
