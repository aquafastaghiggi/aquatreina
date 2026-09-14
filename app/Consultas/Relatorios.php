<?php

declare(strict_types=1);

namespace App\Consultas;

use App\Models\Aula;
use App\Models\Curso;
use App\Models\Organizacao;
use App\Models\ProgressoAula;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Builder;

final class Relatorios
{
    public function cursos(): Builder
    {
        $abandono = Aula::query()
            ->select('aulas.titulo')
            ->join('matriculas', 'matriculas.ultima_aula_id', '=', 'aulas.id')
            ->whereColumn('matriculas.curso_id', 'cursos.id')
            ->where('matriculas.situacao', 'ativa')
            ->groupBy('aulas.id', 'aulas.titulo')
            ->orderByRaw('COUNT(matriculas.id) DESC')
            ->limit(1);

        return Curso::query()
            ->select('cursos.id', 'cursos.titulo')
            ->selectRaw('COUNT(matriculas.id) AS matriculados')
            ->selectRaw("SUM(CASE WHEN matriculas.situacao = 'concluida' THEN 1 ELSE 0 END) AS concluidos")
            ->selectRaw('COALESCE(ROUND(AVG(matriculas.percentual_progresso), 1), 0) AS percentual_medio')
            ->selectSub($abandono, 'aula_maior_abandono')
            ->leftJoin('matriculas', 'matriculas.curso_id', '=', 'cursos.id')
            ->groupBy('cursos.id', 'cursos.titulo')
            ->orderBy('cursos.titulo');
    }

    /** @param array{empresa?: string, situacao?: string, inicio?: string, fim?: string} $filtros */
    public function alunos(array $filtros = []): Builder
    {
        $ultimaAtividade = ProgressoAula::query()
            ->selectRaw('MAX(progresso_aulas.updated_at)')
            ->join('matriculas AS matriculas_atividade', 'matriculas_atividade.id', '=', 'progresso_aulas.matricula_id')
            ->whereColumn('matriculas_atividade.usuario_id', 'usuarios.id');

        return Usuario::query()
            ->select('usuarios.id', 'usuarios.nome', 'usuarios.email', 'usuarios.empresa', 'usuarios.situacao')
            ->selectRaw('COUNT(matriculas.id) AS cursos')
            ->selectRaw('COALESCE(ROUND(AVG(matriculas.percentual_progresso), 1), 0) AS percentual_medio')
            ->selectSub($ultimaAtividade, 'ultima_atividade')
            ->leftJoin('matriculas', 'matriculas.usuario_id', '=', 'usuarios.id')
            ->when($filtros['empresa'] ?? null, fn ($q, $empresa) => $q->where('usuarios.empresa', 'like', "%{$empresa}%"))
            ->when($filtros['situacao'] ?? null, fn ($q, $situacao) => $q->where('usuarios.situacao', $situacao))
            ->when($filtros['inicio'] ?? null, fn ($q, $inicio) => $q->whereDate('usuarios.created_at', '>=', $inicio))
            ->when($filtros['fim'] ?? null, fn ($q, $fim) => $q->whereDate('usuarios.created_at', '<=', $fim))
            ->groupBy('usuarios.id', 'usuarios.nome', 'usuarios.email', 'usuarios.empresa', 'usuarios.situacao')
            ->orderBy('usuarios.nome');
    }

    public function organizacoes(): Builder
    {
        return Organizacao::query()
            ->select('organizacoes.id', 'organizacoes.nome')
            ->selectRaw('COUNT(matriculas.id) AS matriculados')
            ->selectRaw("SUM(CASE WHEN matriculas.situacao = 'concluida' THEN 1 ELSE 0 END) AS concluidos")
            ->leftJoin('usuarios', 'usuarios.organizacao_id', '=', 'organizacoes.id')
            ->leftJoin('matriculas', 'matriculas.usuario_id', '=', 'usuarios.id')
            ->groupBy('organizacoes.id', 'organizacoes.nome')
            ->orderBy('organizacoes.nome');
    }

    public function consulta(string $tipo, array $filtros = []): Builder
    {
        return match ($tipo) {
            'alunos' => $this->alunos($filtros),
            'organizacoes' => $this->organizacoes(),
            default => $this->cursos(),
        };
    }
}
