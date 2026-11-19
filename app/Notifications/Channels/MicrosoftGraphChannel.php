<?php

declare(strict_types=1);

namespace App\Notifications\Channels;

use App\Servicos\Email\ClienteMicrosoftGraph;
use App\Servicos\Email\MensagemMicrosoftGraph;
use Illuminate\Contracts\View\Factory as FabricaView;
use Illuminate\Notifications\Notification;
use RuntimeException;

final class MicrosoftGraphChannel
{
    public function __construct(
        private readonly ClienteMicrosoftGraph $cliente,
        private readonly FabricaView $views,
    ) {}

    public function send(object $notifiable, Notification $notification): void
    {
        if (! method_exists($notification, 'toMicrosoftGraph')) {
            throw new RuntimeException('A notificação não fornece uma mensagem para o Microsoft Graph.');
        }

        $mensagem = $notification->toMicrosoftGraph($notifiable);

        if (! $mensagem instanceof MensagemMicrosoftGraph) {
            throw new RuntimeException('A mensagem do Microsoft Graph é inválida.');
        }

        $rota = $notifiable->routeNotificationFor(self::class, $notification)
            ?? $notifiable->routeNotificationFor('mail', $notification);
        $destinatarios = $this->normalizarDestinatarios($rota);
        $html = $this->views->make('emails.microsoft-graph', ['mensagem' => $mensagem])->render();

        $this->cliente->enviar($destinatarios, $mensagem->assunto, $html);
    }

    /**
     * @return list<string>
     */
    private function normalizarDestinatarios(mixed $rota): array
    {
        $destinatarios = is_array($rota)
            ? (array_is_list($rota) ? $rota : array_keys($rota))
            : [$rota];
        $destinatarios = array_values(array_filter(
            $destinatarios,
            fn (mixed $email): bool => is_string($email) && filter_var($email, FILTER_VALIDATE_EMAIL) !== false,
        ));

        if ($destinatarios === []) {
            throw new RuntimeException('A notificação do Microsoft Graph não possui destinatário válido.');
        }

        return $destinatarios;
    }
}
