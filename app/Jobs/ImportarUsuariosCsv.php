<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Usuario;
use App\Notifications\ImportacaoUsuariosConcluida;
use App\Servicos\Importacao\ImportadorUsuarios;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

final class ImportarUsuariosCsv implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(public string $caminho, public int $administradorId) {}

    public function handle(ImportadorUsuarios $importador): void
    {
        $relatorio = $importador->importar(Storage::disk('local')->path($this->caminho));
        Usuario::query()->find($this->administradorId)?->notify(new ImportacaoUsuariosConcluida($relatorio));
    }
}
