<?php

declare(strict_types=1);

namespace App\Livewire\Publico;

use App\Enums\SituacaoAula;
use App\Enums\SituacaoCurso;
use App\Models\Curso;
use App\Suporte\ApresentacaoCurso;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;

class PaginaCurso extends Component
{
    #[Locked]
    public int $cursoId;

    public function mount(Curso $curso): void
    {
        abort_unless($curso->situacao === SituacaoCurso::Publicado, 404);
        $this->cursoId = $curso->id;
    }

    #[Computed]
    public function curso(): Curso
    {
        return Curso::query()->publicados()
            ->with(['categoria', 'responsavel', 'modulos' => fn ($consulta) => $consulta->orderBy('posicao'),
                'modulos.aulas' => fn ($consulta) => $consulta->where('situacao', SituacaoAula::Publicada)->orderBy('posicao')])
            ->findOrFail($this->cursoId);
    }

    #[Computed]
    public function inscrito(): bool
    {
        return auth()->check() && $this->curso->matriculas()
            ->where('usuario_id', auth()->id())
            ->whereIn('situacao', ['ativa', 'concluida'])
            ->exists();
    }

    public function render(ApresentacaoCurso $apresentacao): View
    {
        $curso = $this->curso;
        $descricao = (string) str($curso->subtitulo ?: strip_tags((string) $curso->descricao))->limit(155);

        return view('livewire.publico.pagina-curso', compact('apresentacao'))
            ->layout('components.layouts.publico', [
                'titulo' => $curso->titulo,
                'descricao' => $descricao,
                'ogTitulo' => $curso->titulo,
                'ogDescricao' => $descricao,
                'ogUrl' => route('cursos.mostrar', $curso),
                'ogImagem' => $apresentacao->urlCapa($curso),
            ]);
    }
}
