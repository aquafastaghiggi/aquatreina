<?php

declare(strict_types=1);

namespace App\Livewire\Aluno;

use App\Enums\SituacaoMatricula;
use App\Models\Aula;
use App\Models\Configuracao;
use App\Models\Curso;
use App\Models\Matricula;
use App\Servicos\Video\ProvedorVideo;
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

    /**
     * Um card por vídeo (não por produto): um mesmo produto com vários vídeos
     * aparece em vários cards, cada um com o seu vídeo embutido direto.
     */
    #[Computed]
    public function produtos(): mixed
    {
        $video = app(ProvedorVideo::class);

        return Curso::query()->publicados()
            ->whereHas('categoria', fn ($consulta) => $consulta
                ->where('slug', config('treina.categoria_trilhas_produto_slug')))
            ->with(['categoria', 'modulos.aulas' => fn ($consulta) => $consulta->publicadas()])
            ->orderBy('posicao')
            ->get()
            ->flatMap(function (Curso $curso) use ($video): iterable {
                $matricula = $this->matriculas->firstWhere('curso_id', $curso->id);

                return $curso->modulos->flatMap->aulas
                    ->whereNotNull('video_id')
                    ->values()
                    ->map(function (Aula $aula) use ($curso, $video, $matricula): Curso {
                        $item = clone $curso;
                        $item->setAttribute('minha_matricula', $matricula);
                        $item->setAttribute('videos_embed', [$video->urlEmbed($aula->video_id)]);

                        return $item;
                    });
            })
            ->values();
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
