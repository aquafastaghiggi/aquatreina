<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\SituacaoMatricula;
use App\Models\Curso;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class ConclusaoPorCurso extends TableWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->hasRole('admin') === true;
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Conclusão por curso')
            ->query(Curso::query()
                ->publicados()
                ->withCount('matriculas')
                ->withCount(['matriculas as matriculas_concluidas_count' => fn (Builder $consulta): Builder => $consulta
                    ->where('situacao', SituacaoMatricula::Concluida)]))
            ->columns([
                TextColumn::make('titulo')->label('Curso'),
                TextColumn::make('matriculas_count')->label('Matrículas'),
                TextColumn::make('matriculas_concluidas_count')->label('Concluídas'),
                TextColumn::make('taxa_conclusao')
                    ->label('Taxa')
                    ->state(fn (Curso $record): string => $record->matriculas_count === 0
                        ? '0%'
                        : round(($record->matriculas_concluidas_count / $record->matriculas_count) * 100).'%'),
            ])
            ->paginated(false);
    }
}
