<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class ResumoSemanalAdmin extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly array $dados) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)->subject('Resumo semanal — Aquafast Treina')
            ->line("Novos usuários: {$this->dados['usuarios']}")
            ->line("Novas matrículas: {$this->dados['matriculas']}")
            ->line("Cursos concluídos: {$this->dados['conclusoes']}")
            ->action('Abrir relatórios', url('/admin/relatorios'));
    }
}
