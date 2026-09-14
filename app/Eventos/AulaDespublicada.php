<?php

declare(strict_types=1);

namespace App\Eventos;

use App\Models\Aula;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class AulaDespublicada
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public readonly Aula $aula) {}
}
