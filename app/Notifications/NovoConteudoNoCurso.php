<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Aula;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class NovoConteudoNoCurso extends Notification
{
    public function __construct(private readonly int $aulaId) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase(object $notifiable): array
    {
        $aula = $this->aula();

        return [
            'titulo' => 'Nova aula disponível',
            'mensagem' => "A aula {$aula->titulo} foi publicada em {$aula->modulo->curso->titulo}.",
            'url' => route('app.aula', [$aula->modulo->curso, $aula]),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $dados = $this->toDatabase($notifiable);

        return (new MailMessage)
            ->subject($dados['titulo'])
            ->greeting("Olá, {$notifiable->nome}!")
            ->line($dados['mensagem'])
            ->action('Assistir nova aula', $dados['url']);
    }

    private function aula(): Aula
    {
        return Aula::query()->with('modulo.curso')->findOrFail($this->aulaId);
    }
}
