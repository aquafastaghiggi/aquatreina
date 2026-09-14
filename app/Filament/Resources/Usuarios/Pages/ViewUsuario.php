<?php

declare(strict_types=1);

namespace App\Filament\Resources\Usuarios\Pages;

use App\Acoes\Usuario\AnonimizarUsuario;
use App\Filament\Resources\Usuarios\UsuarioResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewUsuario extends ViewRecord
{
    protected static string $resource = UsuarioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('anonimizar')
                ->label('Anonimizar usuário')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Anonimizar usuário de forma irreversível?')
                ->modalDescription('Os dados pessoais serão removidos. Matrículas e progresso serão preservados para fins estatísticos.')
                ->action(fn () => app(AnonimizarUsuario::class)->executar($this->record, auth()->user())),
        ];
    }
}
