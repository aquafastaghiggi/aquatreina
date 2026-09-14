<?php

declare(strict_types=1);

namespace App\Filament\Resources\Usuarios;

use App\Acoes\Usuario\AprovarUsuario;
use App\Acoes\Usuario\BloquearUsuario;
use App\Enums\SituacaoUsuario;
use App\Filament\Resources\Usuarios\Pages\ManageUsuarios;
use App\Models\Usuario;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UsuarioResource extends Resource
{
    protected static ?string $model = Usuario::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $navigationLabel = 'Usuários';

    protected static ?string $modelLabel = 'usuário';

    protected static ?string $pluralModelLabel = 'usuários';

    protected static ?string $recordTitleAttribute = 'nome';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nome')
            ->columns([
                TextColumn::make('nome')
                    ->label('Nome')
                    ->searchable(),
                TextColumn::make('email')->label('E-mail')->searchable(),
                TextColumn::make('empresa')->label('Empresa')->placeholder('—'),
                TextColumn::make('situacao')->label('Situação')->badge(),
                TextColumn::make('created_at')->label('Cadastro')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('situacao')
                    ->label('Situação')
                    ->options(collect(SituacaoUsuario::cases())->mapWithKeys(
                        fn (SituacaoUsuario $situacao): array => [$situacao->value => $situacao->rotulo()],
                    )),
            ])
            ->recordActions([
                Action::make('aprovar')
                    ->label('Aprovar')
                    ->color('success')
                    ->visible(fn (Usuario $record): bool => $record->situacao !== SituacaoUsuario::Ativo)
                    ->requiresConfirmation()
                    ->action(fn (Usuario $record) => app(AprovarUsuario::class)->executar($record, auth()->user())),
                Action::make('bloquear')
                    ->label('Bloquear')
                    ->color('danger')
                    ->visible(fn (Usuario $record): bool => $record->situacao !== SituacaoUsuario::Bloqueado)
                    ->requiresConfirmation()
                    ->action(fn (Usuario $record) => app(BloquearUsuario::class)->executar($record, auth()->user())),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageUsuarios::route('/'),
        ];
    }
}
