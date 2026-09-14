<?php

declare(strict_types=1);

namespace App\Livewire\Aluno;

use App\Acoes\Progresso\ConcluirAula;
use App\Enums\SituacaoAula;
use App\Enums\SituacaoMatricula;
use App\Models\Aula;
use App\Models\Comentario;
use App\Models\Configuracao;
use App\Models\Curso;
use App\Models\Matricula;
use App\Models\ProgressoAula;
use App\Servicos\Video\FabricaProvedorVideo;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class SalaDeAula extends Component
{
    #[Locked]
    public int $cursoId;

    #[Locked]
    public int $aulaId;

    #[Locked]
    public int $matriculaId;

    public int $percentualCurso = 0;

    public function mount(Curso $curso, Aula $aula): void
    {
        $aula->loadMissing('modulo');
        abort_unless($aula->situacao === SituacaoAula::Publicada, 404);
        $matricula = Matricula::query()
            ->where('usuario_id', auth()->id())
            ->where('curso_id', $aula->modulo->curso_id)
            ->whereIn('situacao', [SituacaoMatricula::Ativa->value, SituacaoMatricula::Concluida->value])
            ->first();
        abort_if($matricula === null, 403);
        abort_unless($curso->id === $aula->modulo->curso_id, 404);

        $this->cursoId = $curso->id;
        $this->aulaId = $aula->id;
        $this->matriculaId = $matricula->id;
        $this->percentualCurso = $matricula->percentual_progresso;
    }

    #[Computed]
    public function curso(): Curso
    {
        return Curso::query()
            ->with(['modulos' => fn ($consulta) => $consulta->orderBy('posicao'),
                'modulos.aulas' => fn ($consulta) => $consulta->publicadas()->orderBy('posicao')])
            ->findOrFail($this->cursoId);
    }

    #[Computed]
    public function aula(): Aula
    {
        return Aula::query()->with(['modulo', 'materiais'])->findOrFail($this->aulaId);
    }

    #[Computed]
    public function matricula(): Matricula
    {
        return Matricula::query()->findOrFail($this->matriculaId);
    }

    #[Computed]
    public function progressoAtual(): ?ProgressoAula
    {
        return ProgressoAula::query()
            ->where('matricula_id', $this->matriculaId)
            ->where('aula_id', $this->aulaId)
            ->first();
    }

    #[Computed]
    public function aulasConcluidas(): array
    {
        return ProgressoAula::query()
            ->where('matricula_id', $this->matriculaId)
            ->whereNotNull('concluido_em')
            ->pluck('aula_id')
            ->map(fn ($id): int => (int) $id)
            ->all();
    }

    #[Computed]
    public function proximaAula(): ?Aula
    {
        $aulas = $this->curso->modulos->flatMap->aulas->values();
        $indice = $aulas->search(fn (Aula $aula): bool => $aula->id === $this->aulaId);

        return $indice === false ? null : $aulas->get($indice + 1);
    }

    #[Computed]
    public function totalComentarios(): int
    {
        return Comentario::query()
            ->visiveis(auth()->user())
            ->where('aula_id', $this->aulaId)
            ->whereNull('comentario_pai_id')
            ->count();
    }

    public function concluirManualmente(ConcluirAula $concluir): void
    {
        abort_unless($this->matricula->situacao === SituacaoMatricula::Ativa, 403);
        $concluir->executar($this->matricula, $this->aula, true);
        $this->atualizarEstado();
    }

    #[On('progresso-atualizado')]
    public function progressoAtualizado(int $percentual_curso): void
    {
        $this->percentualCurso = $percentual_curso;
        unset($this->progressoAtual, $this->aulasConcluidas, $this->matricula);
    }

    #[On('comentario-criado')]
    public function comentarioCriado(): void
    {
        unset($this->totalComentarios);
    }

    public function render(FabricaProvedorVideo $provedores): View
    {
        $urlEmbed = $provedores->criar($this->aula->provedor)->urlEmbed($this->aula->video_id);
        $totalAulas = $this->curso->modulos->sum(fn ($modulo): int => $modulo->aulas->count());

        $intervaloPing = Configuracao::valor('intervalo_ping', config('treina.intervalo_ping'));

        return view('livewire.aluno.sala-de-aula', compact('urlEmbed', 'totalAulas', 'intervaloPing'))
            ->layout('components.layouts.aluno', ['titulo' => $this->aula->titulo]);
    }

    private function atualizarEstado(): void
    {
        unset($this->progressoAtual, $this->aulasConcluidas, $this->matricula);
        $this->percentualCurso = $this->matricula->percentual_progresso;
    }
}
