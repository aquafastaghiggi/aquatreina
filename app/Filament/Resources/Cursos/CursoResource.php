<?php

declare(strict_types=1);

namespace App\Filament\Resources\Cursos;

use App\Acoes\Curso\ArquivarCurso;
use App\Acoes\Curso\PublicarCurso;
use App\Enums\NivelCurso;
use App\Enums\SituacaoCurso;
use App\Excecoes\CursoIncompleto;
use App\Filament\Pages\ConstrutorCurriculo;
use App\Filament\Resources\Cursos\Pages\CreateCurso;
use App\Filament\Resources\Cursos\Pages\EditCurso;
use App\Filament\Resources\Cursos\Pages\ListCursos;
use App\Models\Curso;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\HtmlString;

class CursoResource extends Resource
{
    protected static ?string $model = Curso::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static ?string $modelLabel = 'curso';

    protected static ?string $pluralModelLabel = 'cursos';

    protected static ?string $recordTitleAttribute = 'titulo';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('Curso')->columnSpanFull()->tabs([
                Tab::make('Dados')->schema([
                    TextInput::make('titulo')->label('Título')->required()->maxLength(180),
                    TextInput::make('slug')->label('Slug')->required()->unique(ignoreRecord: true)->maxLength(200),
                    TextInput::make('subtitulo')->label('Subtítulo')->maxLength(255),
                    Select::make('categoria_id')->label('Categoria')->relationship('categoria', 'nome')->searchable()->preload(),
                    Select::make('nivel')->label('Nível')->options(self::opcoesNivel())->required(),
                    Select::make('responsavel_id')
                        ->label('Responsável')
                        ->relationship('responsavel', 'nome', modifyQueryUsing: fn (Builder $query): Builder => $query->role(['instrutor', 'admin']))
                        ->searchable()->preload()->required()->default(auth()->id()),
                    RichEditor::make('descricao')->label('Descrição')->columnSpanFull(),
                    FileUpload::make('capa_caminho')->label('Capa')->image()->disk('public')->directory('capas')->maxSize(5120),
                ]),
                Tab::make('Conteúdo')->schema([
                    Placeholder::make('curriculo')
                        ->label('Construtor de currículo')
                        ->content(fn (?Curso $record): HtmlString => $record === null
                            ? new HtmlString('Salve o curso para começar a montar o currículo.')
                            : new HtmlString('<a class="text-primary-600 font-semibold" href="'.ConstrutorCurriculo::getUrl(['registro' => $record->getKey()]).'">Abrir construtor de currículo</a>')),
                ]),
                Tab::make('Publicação')->schema([
                    Placeholder::make('situacao_atual')->label('Situação')->content(
                        fn (?Curso $record): string => $record?->situacao?->rotulo() ?? 'Rascunho',
                    ),
                    Toggle::make('moderar_comentarios')->label('Moderar comentários'),
                    TextInput::make('posicao')->label('Posição')->numeric()->default(0)->required(),
                    DateTimePicker::make('publicado_em')->label('Publicado em')->disabled(),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading()
            ->columns([
                TextColumn::make('titulo')->label('Título')->searchable()->sortable(),
                TextColumn::make('categoria.nome')->label('Categoria')->placeholder('—'),
                TextColumn::make('responsavel.nome')->label('Responsável'),
                TextColumn::make('nivel')->label('Nível')->badge(),
                TextColumn::make('situacao')->label('Situação')->badge(),
                TextColumn::make('total_aulas')->label('Aulas'),
            ])
            ->filters([
                SelectFilter::make('situacao')->label('Situação')->options(self::opcoesSituacao()),
            ])
            ->recordActions([
                Action::make('curriculo')
                    ->label('Currículo')
                    ->icon(Heroicon::OutlinedQueueList)
                    ->url(fn (Curso $record): string => ConstrutorCurriculo::getUrl(['registro' => $record->getKey()])),
                Action::make('publicar')
                    ->label('Publicar')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Curso $record): bool => $record->situacao !== SituacaoCurso::Publicado)
                    ->action(function (Curso $record): void {
                        Gate::authorize('update', $record);

                        try {
                            app(PublicarCurso::class)->executar($record);
                            Notification::make()->title('Curso publicado')->success()->send();
                        } catch (CursoIncompleto $erro) {
                            Notification::make()
                                ->title('Corrija as pendências para publicar')
                                ->body(implode("\n", array_map(fn (string $item): string => "• {$item}", $erro->pendencias)))
                                ->danger()->persistent()->send();
                        }
                    }),
                Action::make('arquivar')
                    ->label('Arquivar')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->visible(fn (Curso $record): bool => $record->situacao !== SituacaoCurso::Arquivado)
                    ->action(function (Curso $record): void {
                        Gate::authorize('update', $record);
                        app(ArquivarCurso::class)->executar($record);
                        Notification::make()->title('Curso arquivado')->success()->send();
                    }),
                EditAction::make(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $consulta = parent::getEloquentQuery()->with(['categoria', 'responsavel']);
        $usuario = auth()->user();

        return $usuario?->hasRole('admin') === true
            ? $consulta
            : $consulta->where('responsavel_id', $usuario?->getAuthIdentifier());
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCursos::route('/'),
            'create' => CreateCurso::route('/create'),
            'edit' => EditCurso::route('/{record}/edit'),
        ];
    }

    private static function opcoesNivel(): array
    {
        return collect(NivelCurso::cases())->mapWithKeys(
            fn (NivelCurso $nivel): array => [$nivel->value => $nivel->rotulo()],
        )->all();
    }

    private static function opcoesSituacao(): array
    {
        return collect(SituacaoCurso::cases())->mapWithKeys(
            fn (SituacaoCurso $situacao): array => [$situacao->value => $situacao->rotulo()],
        )->all();
    }
}
