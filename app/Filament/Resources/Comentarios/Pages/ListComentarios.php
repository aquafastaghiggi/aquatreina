<?php

declare(strict_types=1);

namespace App\Filament\Resources\Comentarios\Pages;

use App\Filament\Resources\Comentarios\ComentarioResource;
use Filament\Resources\Pages\ListRecords;

class ListComentarios extends ListRecords
{
    protected static string $resource = ComentarioResource::class;
}
