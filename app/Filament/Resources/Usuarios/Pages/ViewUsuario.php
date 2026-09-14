<?php

declare(strict_types=1);

namespace App\Filament\Resources\Usuarios\Pages;

use App\Filament\Resources\Usuarios\UsuarioResource;
use Filament\Resources\Pages\ViewRecord;

class ViewUsuario extends ViewRecord
{
    protected static string $resource = UsuarioResource::class;
}
