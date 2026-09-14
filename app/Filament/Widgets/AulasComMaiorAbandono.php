<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Aula;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class AulasComMaiorAbandono extends TableWidget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->hasRole('admin') === true;
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Aulas com maior abandono')
            ->description('Aulas iniciadas e ainda não concluídas.')
            ->query(Aula::query()
                ->publicadas()
                ->with('modulo.curso')
                ->withCount(['progressos as abandonos_count' => fn (Builder $consulta): Builder => $consulta
                    ->whereNull('concluido_em')
                    ->where('posicao_maxima', '>', 0)])
                ->orderByDesc('abandonos_count')
                ->limit(10))
            ->columns([
                TextColumn::make('titulo')->label('Aula'),
                TextColumn::make('modulo.curso.titulo')->label('Curso'),
                TextColumn::make('abandonos_count')->label('Paradas'),
            ])
            ->paginated(false);
    }
}
