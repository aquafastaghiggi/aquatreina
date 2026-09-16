<?php

declare(strict_types=1);

namespace App\Filament\Resources\Comentarios;

use App\Acoes\Comentario\ModerarComentario;
use App\Acoes\Comentario\ResponderComentario;
use App\Enums\SituacaoComentario;
use App\Filament\Resources\Comentarios\Pages\ListComentarios;
use App\Models\Comentario;
use App\Models\Curso;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;
use UnitEnum;

class ComentarioResource extends Resource
{
    protected static ?string $model = Comentario::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static ?string $navigationLabel = 'Perguntas';

    protected static string|UnitEnum|null $navigationGroup = 'Comunidade';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'pergunta';

    protected static ?string $pluralModelLabel = 'perguntas';

    protected static ?string $recordTitleAttribute = 'corpo';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading()
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('corpo')->label('Pergunta')->limit(70)->searchable(),
                TextColumn::make('usuario.nome')->label('Aluno')->searchable(),
                TextColumn::make('aula.titulo')->label('Aula'),
                TextColumn::make('aula.modulo.curso.titulo')->label('Curso'),
                TextColumn::make('situacao')->label('Situação')->badge(),
                IconColumn::make('fixado')->label('Fixada')->boolean(),
                TextColumn::make('respostas_count')->label('Respostas')->counts('respostas'),
                TextColumn::make('created_at')->label('Enviada em')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([
                Filter::make('sem_resposta')
                    ->label('Sem resposta')
                    ->query(fn (Builder $consulta): Builder => $consulta->whereDoesntHave(
                        'respostas',
                        fn (Builder $respostas): Builder => $respostas->where('e_resposta', true),
                    )),
                SelectFilter::make('situacao')
                    ->label('Situação')
                    ->options(collect(SituacaoComentario::cases())->mapWithKeys(
                        fn (SituacaoComentario $situacao): array => [$situacao->value => $situacao->rotulo()],
                    )),
                SelectFilter::make('curso_id')
                    ->label('Curso')
                    ->options(fn (): array => self::cursosDisponiveis())
                    ->query(fn (Builder $consulta, array $data): Builder => $consulta->when(
                        $data['value'] ?? null,
                        fn (Builder $filtro, $cursoId): Builder => $filtro->whereHas(
                            'aula.modulo',
                            fn (Builder $modulos): Builder => $modulos->where('curso_id', $cursoId),
                        ),
                    )),
            ])
            ->recordActions([
                Action::make('responder')
                    ->label('Responder')
                    ->icon(Heroicon::OutlinedArrowUturnLeft)
                    ->form([
                        Textarea::make('corpo')->label('Resposta')->required()->minLength(3)->maxLength(5000),
                    ])
                    ->authorize(fn (Comentario $record): bool => Gate::allows('respond', $record))
                    ->action(fn (Comentario $record, array $data) => app(ResponderComentario::class)
                        ->executar(auth()->user(), $record, $data['corpo'])),
                Action::make('aprovar')
                    ->label('Aprovar')
                    ->color('success')
                    ->visible(fn (Comentario $record): bool => $record->situacao === SituacaoComentario::Pendente)
                    ->authorize(fn (Comentario $record): bool => Gate::allows('moderate', $record))
                    ->action(fn (Comentario $record) => app(ModerarComentario::class)
                        ->executar(auth()->user(), $record, SituacaoComentario::Aprovado)),
                Action::make('ocultar')
                    ->label('Ocultar')
                    ->color('danger')
                    ->visible(fn (Comentario $record): bool => $record->situacao !== SituacaoComentario::Oculto)
                    ->authorize(fn (Comentario $record): bool => Gate::allows('moderate', $record))
                    ->action(fn (Comentario $record) => app(ModerarComentario::class)
                        ->executar(auth()->user(), $record, SituacaoComentario::Oculto)),
                Action::make('restaurar')
                    ->label('Restaurar')
                    ->visible(fn (Comentario $record): bool => $record->situacao === SituacaoComentario::Oculto)
                    ->authorize(fn (Comentario $record): bool => Gate::allows('moderate', $record))
                    ->action(fn (Comentario $record) => app(ModerarComentario::class)
                        ->executar(auth()->user(), $record, SituacaoComentario::Aprovado)),
                Action::make('fixar')
                    ->label(fn (Comentario $record): string => $record->fixado ? 'Desafixar' : 'Fixar')
                    ->authorize(fn (Comentario $record): bool => Gate::allows('moderate', $record))
                    ->action(fn (Comentario $record) => app(ModerarComentario::class)
                        ->executar(auth()->user(), $record, fixado: ! $record->fixado)),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $consulta = parent::getEloquentQuery()
            ->whereNull('comentario_pai_id')
            ->with(['usuario', 'aula.modulo.curso']);
        $usuario = auth()->user();

        return $usuario?->hasRole('admin') === true
            ? $consulta
            : $consulta->whereHas(
                'aula.modulo.curso',
                fn (Builder $cursos): Builder => $cursos->where('responsavel_id', $usuario?->id),
            );
    }

    public static function getPages(): array
    {
        return ['index' => ListComentarios::route('/')];
    }

    public static function getNavigationBadge(): ?string
    {
        $total = static::getEloquentQuery()->where('situacao', SituacaoComentario::Pendente)->count();

        return $total > 0 ? (string) $total : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    private static function cursosDisponiveis(): array
    {
        $consulta = Curso::query()->orderBy('titulo');
        $usuario = auth()->user();

        if ($usuario?->hasRole('admin') !== true) {
            $consulta->where('responsavel_id', $usuario?->id);
        }

        return $consulta->pluck('titulo', 'id')->all();
    }
}
