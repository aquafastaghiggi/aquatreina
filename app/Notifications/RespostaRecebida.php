<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Comentario;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class RespostaRecebida extends Notification
{
    public function __construct(private readonly int $respostaId) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase(object $notifiable): array
    {
        $resposta = $this->resposta();
        $aula = $resposta->aula;

        return [
            'titulo' => 'Sua pergunta foi respondida',
            'mensagem' => "Há uma nova resposta na aula {$aula->titulo}.",
            'url' => route('app.aula', [$aula->modulo->curso, $aula]).'#perguntas',
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $dados = $this->toDatabase($notifiable);

        return (new MailMessage)
            ->subject($dados['titulo'])
            ->greeting("Olá, {$notifiable->nome}!")
            ->line($dados['mensagem'])
            ->action('Ver resposta', $dados['url']);
    }

    private function resposta(): Comentario
    {
        return Comentario::query()->with('aula.modulo.curso')->findOrFail($this->respostaId);
    }
}
