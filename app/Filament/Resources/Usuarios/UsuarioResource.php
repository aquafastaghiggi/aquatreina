<?php

declare(strict_types=1);

namespace App\Filament\Resources\Usuarios;

use App\Acoes\Usuario\AprovarUsuario;
use App\Acoes\Usuario\BloquearUsuario;
use App\Acoes\Usuario\ExcluirUsuario;
use App\Enums\SituacaoUsuario;
use App\Excecoes\UsuarioPossuiHistorico;
use App\Filament\Resources\Usuarios\Pages\ManageUsuarios;
use App\Filament\Resources\Usuarios\Pages\ViewUsuario;
use App\Filament\Resources\Usuarios\RelationManagers\MatriculasRelationManager;
use App\Models\Usuario;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Gate;
use UnitEnum;

class UsuarioResource extends Resource
{
    protected static ?string $model = Usuario::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $navigationLabel = 'Usuários';

    protected static string|UnitEnum|null $navigationGroup = 'Comunidade';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'usuário';

    protected static ?string $pluralModelLabel = 'usuários';

    protected static ?string $recordTitleAttribute = 'nome';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('nome')->label('Nome'),
            TextEntry::make('email')->label('E-mail'),
            TextEntry::make('telefone')->label('Telefone')->placeholder('—'),
            TextEntry::make('empresa')->label('Empresa')->placeholder('—'),
            TextEntry::make('cargo')->label('Cargo')->placeholder('—'),
            TextEntry::make('situacao')->label('Situação')->badge(),
            TextEntry::make('created_at')->label('Cadastro')->dateTime('d/m/Y H:i'),
        ]);
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
                ViewAction::make(),
                Action::make('aprovar')
                    ->label('Aprovar')
                    ->color('success')
                    ->visible(fn (Usuario $record): bool => $record->situacao !== SituacaoUsuario::Ativo)
                    ->authorize(fn (Usuario $record): bool => Gate::allows('aprovar', $record))
                    ->requiresConfirmation()
                    ->modalHeading('Aprovar cadastro e notificar o aluno?')
                    ->modalDescription('O acesso será liberado e o aluno receberá no e-mail cadastrado uma confirmação enviada por aquafast@aquafast.com.br.')
                    ->modalSubmitActionLabel('Aprovar e notificar')
                    ->action(function (Usuario $record): void {
                        app(AprovarUsuario::class)->executar($record, auth()->user());

                        Notification::make()
                            ->title('Cadastro aprovado')
                            ->body('O aluno receberá por e-mail a confirmação de que o acesso foi liberado.')
                            ->success()
                            ->send();
                    }),
                Action::make('bloquear')
                    ->label('Bloquear')
                    ->color('danger')
                    ->visible(fn (Usuario $record): bool => $record->situacao !== SituacaoUsuario::Bloqueado)
                    ->authorize(fn (Usuario $record): bool => Gate::allows('bloquear', $record))
                    ->requiresConfirmation()
                    ->action(fn (Usuario $record) => app(BloquearUsuario::class)->executar($record, auth()->user())),
                Action::make('excluir')
                    ->label('Excluir')
                    ->icon(Heroicon::OutlinedTrash)
                    ->color('danger')
                    ->visible(fn (Usuario $record): bool => $record->hasRole('aluno'))
                    ->authorize(fn (Usuario $record): bool => Gate::allows('excluir', $record))
                    ->requiresConfirmation()
                    ->modalHeading('Excluir aluno definitivamente?')
                    ->modalDescription('Esta ação serve para limpar cadastros sem histórico e não poderá ser desfeita. Alunos com matrículas, progresso, comentários ou cursos vinculados não serão excluídos.')
                    ->modalSubmitActionLabel('Excluir definitivamente')
                    ->action(function (Usuario $record): void {
                        try {
                            app(ExcluirUsuario::class)->executar($record, auth()->user());
                            Notification::make()->title('Aluno excluído')->success()->send();
                        } catch (UsuarioPossuiHistorico $excecao) {
                            Notification::make()
                                ->title('Não foi possível excluir o aluno')
                                ->body($excecao->getMessage())
                                ->danger()
                                ->persistent()
                                ->send();
                        }
                    }),
            ]);
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasRole('admin') === true;
    }

    public static function getRelations(): array
    {
        return [MatriculasRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageUsuarios::route('/'),
            'view' => ViewUsuario::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $total = static::getEloquentQuery()->where('situacao', SituacaoUsuario::Pendente)->count();

        return $total > 0 ? (string) $total : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
