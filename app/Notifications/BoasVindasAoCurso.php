<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Curso;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class BoasVindasAoCurso extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    /** @var list<int> */
    public array $backoff = [60, 300, 900];

    public function __construct(private readonly int $cursoId)
    {
        $this->onQueue('emails');
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $curso = Curso::query()->findOrFail($this->cursoId);

        return (new MailMessage)
            ->subject('Sua matrícula está pronta')
            ->greeting("Olá, {$notifiable->nome}!")
            ->line("Você já está matriculado em {$curso->titulo}.")
            ->action('Acessar treinamento', route('app.curso', $curso));
    }
}
