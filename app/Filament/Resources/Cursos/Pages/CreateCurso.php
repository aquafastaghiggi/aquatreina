<?php

declare(strict_types=1);

namespace App\Filament\Resources\Cursos\Pages;

use App\Filament\Resources\Cursos\CursoResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCurso extends CreateRecord
{
    protected static string $resource = CursoResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (auth()->user()?->hasRole('instrutor') === true) {
            $data['responsavel_id'] = auth()->id();
        }

        return $data;
    }
}
