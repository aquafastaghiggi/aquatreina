<?php

declare(strict_types=1);

namespace App\Eventos;

use App\Models\Comentario;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class ComentarioRespondido
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public readonly Comentario $resposta) {}
}
