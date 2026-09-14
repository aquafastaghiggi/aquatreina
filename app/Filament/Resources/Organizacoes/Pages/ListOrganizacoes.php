<?php

declare(strict_types=1);

namespace App\Filament\Resources\Organizacoes\Pages;

use App\Filament\Resources\Organizacoes\OrganizacaoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOrganizacoes extends ListRecords
{
    protected static string $resource = OrganizacaoResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
