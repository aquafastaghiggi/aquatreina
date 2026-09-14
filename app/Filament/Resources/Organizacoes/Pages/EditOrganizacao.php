<?php

declare(strict_types=1);

namespace App\Filament\Resources\Organizacoes\Pages;

use App\Filament\Resources\Organizacoes\OrganizacaoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOrganizacao extends EditRecord
{
    protected static string $resource = OrganizacaoResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
