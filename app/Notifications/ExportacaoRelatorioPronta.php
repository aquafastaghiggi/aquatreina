<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class ExportacaoRelatorioPronta extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly string $caminho) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Exportação do Aquafast Treina pronta')
            ->line('Sua exportação foi processada.')
            ->action('Baixar arquivo', route('admin.exportacoes.baixar', ['arquivo' => basename($this->caminho)]));
    }

    public function toArray(object $notifiable): array
    {
        return ['mensagem' => 'Exportação pronta', 'caminho' => $this->caminho];
    }
}
