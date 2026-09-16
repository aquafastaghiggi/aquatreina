<?php

declare(strict_types=1);

namespace App\Filament\Resources\Categorias;

use App\Filament\Resources\Categorias\Pages\CreateCategoria;
use App\Filament\Resources\Categorias\Pages\EditCategoria;
use App\Filament\Resources\Categorias\Pages\ListCategorias;
use App\Models\Categoria;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class CategoriaResource extends Resource
{
    protected static ?string $model = Categoria::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static string|UnitEnum|null $navigationGroup = 'Conteúdo';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'categoria';

    protected static ?string $pluralModelLabel = 'categorias';

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasRole('admin') === true;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('nome')->label('Nome')->required()->maxLength(120),
            TextInput::make('slug')->label('Slug')->required()->unique(ignoreRecord: true)->maxLength(140),
            ColorPicker::make('cor')->label('Cor'),
            TextInput::make('posicao')->label('Posição')->numeric()->default(0)->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('posicao')
            ->reorderable('posicao')
            ->columns([
                TextColumn::make('nome')->label('Nome')->searchable(),
                TextColumn::make('slug')->label('Slug'),
                ColorColumn::make('cor')->label('Cor'),
                TextColumn::make('posicao')->label('Posição')->sortable(),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCategorias::route('/'),
            'create' => CreateCategoria::route('/create'),
            'edit' => EditCategoria::route('/{record}/edit'),
        ];
    }
}
