<?php

declare(strict_types=1);

namespace App\Livewire\Aluno;

use App\Enums\SituacaoMatricula;
use App\Models\Configuracao;
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
            ->with(['curso.categoria', 'curso.modulos.aulas', 'ultimaAula.modulo'])
            ->latest('matriculado_em')
            ->get();
    }

    #[Computed]
    public function produtos(): mixed
    {
        return Curso::query()->publicados()
            ->whereHas('categoria', fn ($consulta) => $consulta
                ->where('slug', config('treina.categoria_trilhas_produto_slug')))
            ->with('categoria')
            ->orderBy('posicao')
            ->get()
            ->map(function (Curso $curso): Curso {
                $curso->setAttribute('minha_matricula', $this->matriculas->firstWhere('curso_id', $curso->id));

                return $curso;
            });
    }

    /**
     * Matrículas fora da trilha de produtos (categorias legadas, se existirem).
     * Mantido para não esconder conteúdo antigo do aluno.
     */
    #[Computed]
    public function outrasMatriculas(): mixed
    {
        $idsProdutos = $this->produtos->pluck('id');

        return $this->matriculas->reject(fn (Matricula $matricula): bool => $idsProdutos->contains($matricula->curso_id));
    }

    public function render(ApresentacaoCurso $apresentacao): View
    {
        return view('livewire.aluno.painel', [
            'apresentacao' => $apresentacao,
            'emAndamento' => $this->matriculas->where('situacao', SituacaoMatricula::Ativa),
            'outrasEmAndamento' => $this->outrasMatriculas->where('situacao', SituacaoMatricula::Ativa),
            'outrasConcluidas' => $this->outrasMatriculas->where('situacao', SituacaoMatricula::Concluida),
            'textoBoasVindas' => Configuracao::valor('texto_boas_vindas', ''),
        ])->layout('components.layouts.aluno', ['titulo' => 'Minha área']);
    }
}
