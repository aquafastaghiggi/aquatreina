<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Exportacoes\RelatorioExport;
use App\Models\Usuario;
use App\Notifications\ExportacaoRelatorioPronta;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Maatwebsite\Excel\Facades\Excel;

final class GerarExportacaoRelatorio implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(public string $tipo, public array $filtros, public int $usuarioId) {}

    public function handle(): void
    {
        $caminho = 'exportacoes/'.now()->format('YmdHis')."-{$this->tipo}-{$this->usuarioId}.xlsx";
        Excel::store(new RelatorioExport($this->tipo, $this->filtros), $caminho, 'local');
        Usuario::query()->find($this->usuarioId)?->notify(new ExportacaoRelatorioPronta($caminho));
    }
}
