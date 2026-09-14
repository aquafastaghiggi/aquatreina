<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Jobs\ImportarUsuariosCsv;
use App\Servicos\Importacao\ImportadorUsuarios;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\WithFileUploads;

class ImportarUsuarios extends Page
{
    use WithFileUploads;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = 'usuarios/importar';

    protected string $view = 'filament.pages.importar-usuarios';

    public mixed $arquivo = null;

    public array $linhas = [];

    public ?string $caminho = null;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('admin') === true;
    }

    public function previsualizar(ImportadorUsuarios $importador): void
    {
        $this->validate(['arquivo' => ['required', 'file', 'mimes:csv,txt', 'max:10240']]);
        $this->caminho = $this->arquivo->store('importacoes', 'local');
        $this->linhas = $importador->previsualizar(Storage::disk('local')->path($this->caminho));
    }

    public function confirmar(): void
    {
        abort_if($this->caminho === null, 422);
        ImportarUsuariosCsv::dispatch($this->caminho, (int) auth()->id());
        $this->reset('arquivo', 'linhas', 'caminho');
        Notification::make()->title('Importação enviada para a fila')->success()->send();
    }
}
