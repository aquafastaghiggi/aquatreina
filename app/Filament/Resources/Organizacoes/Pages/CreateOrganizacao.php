<?php

declare(strict_types=1);

namespace App\Filament\Resources\Organizacoes\Pages;

use App\Filament\Resources\Organizacoes\OrganizacaoResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOrganizacao extends CreateRecord
{
    protected static string $resource = OrganizacaoResource::class;
}
