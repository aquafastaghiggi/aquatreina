<?php

declare(strict_types=1);

namespace App\Exportacoes;

use App\Consultas\Relatorios;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

final class RelatorioExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(public string $tipo, public array $filtros = []) {}

    public function query(): Builder
    {
        return app(Relatorios::class)->consulta($this->tipo, $this->filtros);
    }

    public function headings(): array
    {
        return match ($this->tipo) {
            'alunos' => ['Nome', 'E-mail', 'Empresa', 'Situação', 'Cursos', 'Progresso médio (%)', 'Última atividade'],
            'organizacoes' => ['Organização', 'Matriculados', 'Concluídos'],
            default => ['Curso', 'Matriculados', 'Concluídos', 'Progresso médio (%)', 'Aula com maior abandono'],
        };
    }

    public function map($linha): array
    {
        return match ($this->tipo) {
            'alunos' => [$linha->nome, $linha->email, $linha->empresa, $linha->situacao, $linha->cursos, $linha->percentual_medio, $linha->ultima_atividade],
            'organizacoes' => [$linha->nome, $linha->matriculados, $linha->concluidos],
            default => [$linha->titulo, $linha->matriculados, $linha->concluidos, $linha->percentual_medio, $linha->aula_maior_abandono],
        };
    }
}
