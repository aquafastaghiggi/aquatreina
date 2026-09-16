<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Consultas\Relatorios as ConsultasRelatorios;
use App\Exportacoes\RelatorioExport;
use App\Jobs\GerarExportacaoRelatorio;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use UnitEnum;

class Relatorios extends Page
{
    use WithPagination;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?string $navigationLabel = 'Relatórios';

    protected static string|UnitEnum|null $navigationGroup = 'Operação';

    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'relatorios';

    protected string $view = 'filament.pages.relatorios';

    public string $aba = 'cursos';

    public string $empresa = '';

    public string $situacao = '';

    public string $inicio = '';

    public string $fim = '';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('admin') === true;
    }

    #[Computed]
    public function linhas(): LengthAwarePaginator
    {
        return app(ConsultasRelatorios::class)->consulta($this->aba, $this->filtros())->paginate(25);
    }

    public function mudarAba(string $aba): void
    {
        abort_unless(in_array($aba, ['cursos', 'alunos', 'organizacoes'], true), 404);
        $this->aba = $aba;
        $this->resetPage();
        unset($this->linhas);
    }

    public function updated(): void
    {
        $this->resetPage();
        unset($this->linhas);
    }

    public function exportar(): mixed
    {
        $consulta = app(ConsultasRelatorios::class)->consulta($this->aba, $this->filtros());
        $total = DB::query()->fromSub($consulta->toBase(), 'relatorio')->count();

        if ($total > 1000) {
            GerarExportacaoRelatorio::dispatch($this->aba, $this->filtros(), (int) auth()->id());
            Notification::make()->title('Exportação enviada para processamento')->success()->send();

            return null;
        }

        return Excel::download(new RelatorioExport($this->aba, $this->filtros()), "relatorio-{$this->aba}.xlsx");
    }

    private function filtros(): array
    {
        return array_filter([
            'empresa' => trim($this->empresa), 'situacao' => $this->situacao,
            'inicio' => $this->inicio, 'fim' => $this->fim,
        ]);
    }
}
