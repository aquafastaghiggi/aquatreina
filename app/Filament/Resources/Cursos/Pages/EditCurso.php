<?php

declare(strict_types=1);

namespace App\Filament\Resources\Cursos\Pages;

use App\Filament\Resources\Cursos\CursoResource;
use Filament\Resources\Pages\EditRecord;

class EditCurso extends EditRecord
{
    protected static string $resource = CursoResource::class;
}
