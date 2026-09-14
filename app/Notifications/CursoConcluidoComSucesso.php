<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Curso;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class CursoConcluidoComSucesso extends Notification
{
    public function __construct(private readonly int $cursoId) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $curso = Curso::query()->findOrFail($this->cursoId);

        return (new MailMessage)
            ->subject('Treinamento concluído')
            ->greeting("Parabéns, {$notifiable->nome}!")
            ->line("Você concluiu o treinamento {$curso->titulo}.")
            ->action('Rever treinamento', route('app.curso', $curso));
    }
}
