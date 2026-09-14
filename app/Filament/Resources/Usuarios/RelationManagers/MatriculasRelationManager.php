<?php

declare(strict_types=1);

namespace App\Filament\Resources\Usuarios\RelationManagers;

use App\Acoes\Matricula\CancelarMatricula;
use App\Acoes\Matricula\MatricularAluno;
use App\Enums\OrigemMatricula;
use App\Enums\SituacaoCurso;
use App\Enums\SituacaoMatricula;
use App\Models\Curso;
use App\Models\Matricula;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MatriculasRelationManager extends RelationManager
{
    protected static string $relationship = 'matriculas';

    protected static ?string $title = 'Matrículas';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('curso.titulo')->label('Curso')->searchable(),
                TextColumn::make('situacao')->label('Situação')->badge(),
                TextColumn::make('percentual_progresso')->label('Progresso')->suffix('%'),
                TextColumn::make('origem')->label('Origem')->badge(),
                TextColumn::make('matriculado_em')->label('Matrícula')->dateTime('d/m/Y H:i'),
            ])
            ->headerActions([
                Action::make('matricular')
                    ->label('Matricular em curso')
                    ->visible(fn (): bool => auth()->user()?->hasRole('admin') === true)
                    ->form([
                        Select::make('curso_id')
                            ->label('Curso')
                            ->options(fn (): array => Curso::query()
                                ->where('situacao', SituacaoCurso::Publicado)
                                ->orderBy('titulo')
                                ->pluck('titulo', 'id')
                                ->all())
                            ->searchable()
                            ->required(),
                    ])
                    ->action(function (array $data): void {
                        $curso = Curso::query()->findOrFail($data['curso_id']);
                        app(MatricularAluno::class)->executar(
                            $this->getOwnerRecord(),
                            $curso,
                            OrigemMatricula::Admin,
                            auth()->user(),
                        );
                        Notification::make()->title('Aluno matriculado')->success()->send();
                    }),
            ])
            ->recordActions([
                Action::make('cancelar')
                    ->label('Cancelar')
                    ->color('danger')
                    ->visible(fn (Matricula $record): bool => auth()->user()?->hasRole('admin') === true
                        && $record->situacao !== SituacaoMatricula::Cancelada)
                    ->requiresConfirmation()
                    ->action(function (Matricula $record): void {
                        app(CancelarMatricula::class)->executar($record, auth()->user());
                        Notification::make()->title('Matrícula cancelada')->success()->send();
                    }),
            ]);
    }
}
