<?php

declare(strict_types=1);

namespace App\Livewire\Aluno;

use App\Enums\SituacaoMatricula;
use App\Models\Curso;
use App\Models\Matricula;
use App\Suporte\ApresentacaoCurso;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Painel extends Component
{
    public function mount(): void
    {
        abort_unless(auth()->check(), 403);
    }

    #[Computed]
    public function matriculas(): mixed
    {
        return Matricula::query()
            ->where('usuario_id', auth()->id())
            ->with(['curso.categoria', 'curso.modulos.aulas'])
            ->latest('matriculado_em')
            ->get();
    }

    #[Computed]
    public function sugestoes(): mixed
    {
        return Curso::query()->publicados()
            ->whereDoesntHave('matriculas', fn ($consulta) => $consulta
                ->where('usuario_id', auth()->id())
                ->whereIn('situacao', [SituacaoMatricula::Ativa->value, SituacaoMatricula::Concluida->value]))
            ->with(['categoria', 'modulos.aulas'])
            ->orderBy('posicao')
            ->limit(3)
            ->get();
    }

    public function render(ApresentacaoCurso $apresentacao): View
    {
        return view('livewire.aluno.painel', [
            'apresentacao' => $apresentacao,
            'emAndamento' => $this->matriculas->where('situacao', SituacaoMatricula::Ativa),
            'concluidas' => $this->matriculas->where('situacao', SituacaoMatricula::Concluida),
        ])->layout('components.layouts.aluno', ['titulo' => 'Minha área']);
    }
}
