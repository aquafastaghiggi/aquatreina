<?php

declare(strict_types=1);

namespace App\Livewire\Aluno;

use App\Enums\NivelCurso;
use App\Models\Categoria;
use App\Models\Curso;
use App\Suporte\ApresentacaoCurso;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Catalogo extends Component
{
    use WithPagination;

    #[Url]
    public string $busca = '';

    #[Url]
    public string $categoria = '';

    #[Url]
    public string $nivel = '';

    public function mount(): void
    {
        abort_unless(auth()->check(), 403);
    }

    #[Computed]
    public function cursos(): LengthAwarePaginator
    {
        return Curso::query()->publicados()
            ->with(['categoria', 'modulos.aulas'])
            ->withExists(['matriculas as inscrito' => fn (Builder $consulta): Builder => $consulta
                ->where('usuario_id', auth()->id())
                ->whereIn('situacao', ['ativa', 'concluida'])])
            ->when($this->busca !== '', fn (Builder $consulta): Builder => $consulta->where(
                fn (Builder $texto): Builder => $texto
                    ->where('titulo', 'like', "%{$this->busca}%")
                    ->orWhere('subtitulo', 'like', "%{$this->busca}%"),
            ))
            ->when($this->categoria !== '', fn (Builder $consulta): Builder => $consulta
                ->whereHas('categoria', fn (Builder $categoria): Builder => $categoria->where('slug', $this->categoria)))
            ->when($this->nivel !== '', fn (Builder $consulta): Builder => $consulta->where('nivel', $this->nivel))
            ->orderBy('posicao')
            ->paginate(12);
    }

    #[Computed]
    public function categorias(): mixed
    {
        return Categoria::query()->orderBy('posicao')->get(['id', 'nome', 'slug']);
    }

    public function limparFiltros(): void
    {
        $this->reset('busca', 'categoria', 'nivel');
        $this->resetPage();
    }

    public function updatedBusca(): void
    {
        $this->resetPage();
    }

    public function updatedCategoria(): void
    {
        $this->resetPage();
    }

    public function updatedNivel(): void
    {
        $this->resetPage();
    }

    public function render(ApresentacaoCurso $apresentacao): View
    {
        return view('livewire.aluno.catalogo', [
            'apresentacao' => $apresentacao,
            'niveis' => NivelCurso::cases(),
        ])->layout('components.layouts.aluno', ['titulo' => 'Catálogo']);
    }
}
