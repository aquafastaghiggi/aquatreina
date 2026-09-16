<?php

declare(strict_types=1);

namespace App\Filament\Resources\Usuarios\Pages;

use App\Acoes\Usuario\AnonimizarUsuario;
use App\Filament\Resources\Usuarios\UsuarioResource;
use App\Models\Usuario;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Gate;

class ViewUsuario extends ViewRecord
{
    protected static string $resource = UsuarioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('anonimizar')
                ->label('Anonimizar usuário')
                ->color('danger')
                ->authorize(fn (Usuario $record): bool => Gate::allows('anonimizar', $record))
                ->requiresConfirmation()
                ->modalHeading('Anonimizar usuário de forma irreversível?')
                ->modalDescription('Os dados pessoais serão removidos. Matrículas e progresso serão preservados para fins estatísticos.')
                ->action(fn () => app(AnonimizarUsuario::class)->executar($this->record, auth()->user())),
        ];
    }
}
