<?php

declare(strict_types=1);

namespace App\Filament\Resources\Organizacoes;

use App\Enums\TipoOrganizacao;
use App\Filament\Resources\Organizacoes\Pages\CreateOrganizacao;
use App\Filament\Resources\Organizacoes\Pages\EditOrganizacao;
use App\Filament\Resources\Organizacoes\Pages\ListOrganizacoes;
use App\Models\Organizacao;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrganizacaoResource extends Resource
{
    protected static ?string $model = Organizacao::class;

    protected static ?string $slug = 'organizacoes';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice;

    protected static ?string $modelLabel = 'organização';

    protected static ?string $pluralModelLabel = 'organizações';

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasRole('admin') === true;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('nome')->label('Nome')->required()->maxLength(160),
            TextInput::make('cnpj')->label('CNPJ')->unique(ignoreRecord: true)->maxLength(18),
            Select::make('tipo')->label('Tipo')->options(self::opcoesTipo())->required(),
            TextInput::make('uf')->label('UF')->length(2),
            Toggle::make('ativa')->label('Ativa')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('nome')->label('Nome')->searchable(),
            TextColumn::make('cnpj')->label('CNPJ')->placeholder('—'),
            TextColumn::make('tipo')->label('Tipo')->badge(),
            TextColumn::make('uf')->label('UF'),
            IconColumn::make('ativa')->label('Ativa')->boolean(),
        ])->recordActions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrganizacoes::route('/'),
            'create' => CreateOrganizacao::route('/create'),
            'edit' => EditOrganizacao::route('/{record}/edit'),
        ];
    }

    private static function opcoesTipo(): array
    {
        return collect(TipoOrganizacao::cases())
            ->mapWithKeys(fn (TipoOrganizacao $tipo): array => [$tipo->value => $tipo->rotulo()])
            ->all();
    }
}
