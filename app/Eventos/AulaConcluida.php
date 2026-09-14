<?php

declare(strict_types=1);

namespace App\Eventos;

use App\Models\ProgressoAula;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class AulaConcluida
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public readonly ProgressoAula $progresso) {}
}
