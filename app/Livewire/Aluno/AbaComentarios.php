<?php

declare(strict_types=1);

namespace App\Livewire\Aluno;

use App\Acoes\Comentario\CriarComentario;
use App\Enums\SituacaoMatricula;
use App\Models\Aula;
use App\Models\Comentario;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;

class AbaComentarios extends Component
{
    use WithPagination;

    #[Locked]
    public int $aulaId;

    public string $corpo = '';

    public function mount(int $aulaId): void
    {
        $aula = Aula::query()->with('modulo')->findOrFail($aulaId);
        $matriculado = auth()->user()->matriculas()
            ->where('curso_id', $aula->modulo->curso_id)
            ->whereIn('situacao', [SituacaoMatricula::Ativa, SituacaoMatricula::Concluida])
            ->exists();
        abort_unless($matriculado, 403);

        $this->aulaId = $aula->id;
    }

    #[Computed]
    public function aula(): Aula
    {
        return Aula::query()->with('modulo.curso')->findOrFail($this->aulaId);
    }

    #[Computed]
    public function podePerguntar(): bool
    {
        return Gate::allows('create', [Comentario::class, $this->aula]);
    }

    #[Computed]
    public function comentarios(): LengthAwarePaginator
    {
        $usuario = auth()->user();

        return Comentario::query()
            ->visiveis($usuario)
            ->where('aula_id', $this->aulaId)
            ->whereNull('comentario_pai_id')
            ->with([
                'usuario',
                'respostas' => fn ($consulta) => $consulta->visiveis($usuario),
                'respostas.usuario',
            ])
            ->orderByDesc('fixado')
            ->orderByDesc('created_at')
            ->paginate(10, pageName: "comentarios-{$this->aulaId}");
    }

    public function enviar(CriarComentario $criar): void
    {
        $dados = $this->validate([
            'corpo' => ['required', 'string', 'min:3', 'max:5000'],
        ]);
        Gate::authorize('create', [Comentario::class, $this->aula]);
        $criar->executar(auth()->user(), $this->aula, $dados['corpo']);
        $this->reset('corpo');
        $this->resetPage(pageName: "comentarios-{$this->aulaId}");
        unset($this->comentarios);
        $this->dispatch('comentario-criado');
        session()->flash('comentario_sucesso', $this->aula->modulo->curso->moderar_comentarios
            ? 'Pergunta enviada e aguardando análise.'
            : 'Pergunta publicada.');
    }

    public function render(): View
    {
        return view('livewire.aluno.aba-comentarios');
    }
}
