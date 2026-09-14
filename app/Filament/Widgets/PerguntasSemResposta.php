<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\SituacaoComentario;
use App\Filament\Resources\Comentarios\ComentarioResource;
use App\Models\Comentario;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class PerguntasSemResposta extends TableWidget
{
    protected static ?int $sort = -4;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'instrutor']) === true;
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Perguntas sem resposta')
            ->description('Abra uma pergunta para acessar a fila de moderação.')
            ->query($this->consulta())
            ->columns([
                TextColumn::make('corpo')
                    ->label('Pergunta')
                    ->limit(80)
                    ->url(fn (): string => ComentarioResource::getUrl('index')),
                TextColumn::make('aula.modulo.curso.titulo')->label('Curso'),
                TextColumn::make('usuario.nome')->label('Aluno'),
                TextColumn::make('created_at')->label('Há')->since(),
            ])
            ->paginated(false);
    }

    private function consulta(): Builder
    {
        $consulta = Comentario::query()
            ->whereNull('comentario_pai_id')
            ->whereIn('situacao', [SituacaoComentario::Pendente, SituacaoComentario::Aprovado])
            ->whereDoesntHave('respostas', fn (Builder $respostas): Builder => $respostas->where('e_resposta', true))
            ->with(['usuario', 'aula.modulo.curso'])
            ->oldest()
            ->limit(10);

        if (auth()->user()?->hasRole('admin') !== true) {
            $consulta->whereHas(
                'aula.modulo.curso',
                fn (Builder $cursos): Builder => $cursos->where('responsavel_id', auth()->id()),
            );
        }

        return $consulta;
    }
}
