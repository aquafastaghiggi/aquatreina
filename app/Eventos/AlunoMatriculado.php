<?php

declare(strict_types=1);

namespace App\Eventos;

use App\Models\Matricula;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class AlunoMatriculado
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public readonly Matricula $matricula) {}
}
