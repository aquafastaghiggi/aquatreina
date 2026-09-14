<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Eventos\ComentarioRespondido;
use App\Notifications\RespostaRecebida;
use Illuminate\Contracts\Queue\ShouldQueue;

final class NotificarAutorAoComentarioRespondido implements ShouldQueue
{
    public string $queue = 'emails';

    public int $tries = 3;

    /** @var list<int> */
    public array $backoff = [60, 300, 900];

    public function handle(ComentarioRespondido $evento): void
    {
        $resposta = $evento->resposta->loadMissing('comentarioPai.usuario');
        $resposta->comentarioPai->usuario->notify(new RespostaRecebida($resposta->id));
    }
}
