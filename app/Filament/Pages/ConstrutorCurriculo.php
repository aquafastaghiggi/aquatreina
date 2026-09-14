<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Acoes\Curso\ReordenarCurriculo;
use App\Acoes\Curso\SalvarAula;
use App\Enums\ProvedorVideo;
use App\Enums\SituacaoAula;
use App\Models\Aula;
use App\Models\Curso;
use App\Models\Material;
use App\Models\Modulo;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Features\SupportFileUploads\WithFileUploads;

class ConstrutorCurriculo extends Page
{
    use WithFileUploads;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQueueList;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = 'cursos/{registro}/curriculo';

    protected string $view = 'filament.pages.construtor-curriculo';

    #[Locked]
    public int $cursoId;

    #[Locked]
    public ?int $aulaSelecionadaId = null;

    public string $novoModuloTitulo = '';

    public string $aulaTitulo = '';

    public string $aulaDescricao = '';

    public string $linkVideo = '';

    public int $duracaoSegundos = 0;

    public bool $amostraGratuita = false;

    public string $situacaoAula = 'rascunho';

    public string $tituloMaterial = '';

    public mixed $arquivoMaterial = null;

    public ?string $mensagemMetadados = null;

    public function mount(string $registro): void
    {
        $curso = Curso::query()->findOrFail($registro);
        Gate::authorize('update', $curso);
        $this->cursoId = $curso->id;
    }

    #[Computed]
    public function curso(): Curso
    {
        return Curso::query()
            ->with(['modulos.aulas.materiais'])
            ->findOrFail($this->cursoId);
    }

    public function getTitle(): string
    {
        return 'Currículo: '.$this->curso->titulo;
    }

    public function criarModulo(): void
    {
        $dados = $this->validate(['novoModuloTitulo' => ['required', 'string', 'max:180']]);
        $curso = $this->cursoAutorizado();
        $curso->modulos()->create([
            'titulo' => $dados['novoModuloTitulo'],
            'posicao' => ((int) $curso->modulos()->max('posicao')) + 1,
        ]);
        $this->reset('novoModuloTitulo');
    }

    public function renomearModulo(int $moduloId, string $titulo): void
    {
        $modulo = $this->moduloAutorizado($moduloId);
        $titulo = trim($titulo);

        if ($titulo !== '') {
            $modulo->update(['titulo' => Str::limit($titulo, 180, '')]);
        }
    }

    public function excluirModulo(int $moduloId): void
    {
        $this->moduloAutorizado($moduloId)->delete();

        if ($this->aulaSelecionadaId !== null && Aula::query()->find($this->aulaSelecionadaId) === null) {
            $this->limparAulaSelecionada();
        }
    }

    public function criarAula(int $moduloId): void
    {
        $modulo = $this->moduloAutorizado($moduloId);
        $aula = $modulo->aulas()->create([
            'titulo' => 'Nova aula',
            'slug' => 'nova-aula-'.Str::lower(Str::random(6)),
            'provedor' => ProvedorVideo::Youtube,
            'posicao' => ((int) $modulo->aulas()->max('posicao')) + 1,
        ]);
        $this->selecionarAula($aula->id);
    }

    public function selecionarAula(int $aulaId): void
    {
        $aula = $this->aulaAutorizada($aulaId);
        $this->aulaSelecionadaId = $aula->id;
        $this->aulaTitulo = $aula->titulo;
        $this->aulaDescricao = (string) $aula->descricao;
        $this->linkVideo = '';
        $this->duracaoSegundos = $aula->duracao_segundos;
        $this->amostraGratuita = $aula->amostra_gratuita;
        $this->situacaoAula = $aula->situacao->value;
        $this->mensagemMetadados = null;
    }

    public function salvarAula(SalvarAula $salvar): void
    {
        $dados = $this->validate([
            'aulaTitulo' => ['required', 'string', 'max:180'],
            'aulaDescricao' => ['nullable', 'string'],
            'linkVideo' => ['nullable', 'string', 'max:500'],
            'duracaoSegundos' => ['required', 'integer', 'min:0'],
            'amostraGratuita' => ['boolean'],
            'situacaoAula' => ['required', 'in:rascunho,publicada'],
        ]);
        $aula = $salvar->executar($this->aulaAutorizada((int) $this->aulaSelecionadaId), [
            'titulo' => $dados['aulaTitulo'], 'descricao' => $dados['aulaDescricao'],
            'link_video' => $dados['linkVideo'], 'duracao_segundos' => $dados['duracaoSegundos'],
            'amostra_gratuita' => $dados['amostraGratuita'], 'situacao' => $dados['situacaoAula'],
        ]);
        $this->aulaTitulo = $aula->titulo;
        $this->duracaoSegundos = $aula->duracao_segundos;
        $this->mensagemMetadados = $this->linkVideo === '' ? null : ($aula->duracao_segundos > 0
            ? 'Título e duração preenchidos automaticamente.'
            : 'Vídeo identificado; informe a duração manualmente.');
        Notification::make()->title('Aula salva')->success()->send();
    }

    public function duplicarAula(int $aulaId): void
    {
        $original = $this->aulaAutorizada($aulaId);
        $copia = $original->replicate(['publicada_em']);
        $copia->titulo = $original->titulo.' (cópia)';
        $copia->slug = Str::slug($copia->titulo).'-'.Str::lower(Str::random(5));
        $copia->situacao = SituacaoAula::Rascunho;
        $copia->posicao = ((int) $original->modulo->aulas()->max('posicao')) + 1;
        $copia->save();
        $this->selecionarAula($copia->id);
    }

    public function excluirAula(int $aulaId): void
    {
        $this->aulaAutorizada($aulaId)->delete();

        if ($this->aulaSelecionadaId === $aulaId) {
            $this->limparAulaSelecionada();
        }
    }

    /** @param list<array{id: int, aulas: list<int>}> $ordem */
    public function reordenar(array $ordem, ReordenarCurriculo $reordenar): void
    {
        $reordenar->executar($this->cursoAutorizado(), $ordem);
        Notification::make()->title('Ordem salva')->success()->send();
    }

    public function enviarMaterial(): void
    {
        $dados = $this->validate([
            'tituloMaterial' => ['required', 'string', 'max:180'],
            'arquivoMaterial' => ['required', 'file', 'max:20480', 'mimes:pdf,xlsx,docx,pptx,png,jpg,jpeg,zip'],
        ]);
        $aula = $this->aulaAutorizada((int) $this->aulaSelecionadaId);
        $arquivo = $dados['arquivoMaterial'];
        $caminho = $arquivo->store("aulas/{$aula->id}", 'materiais');
        $aula->materiais()->create([
            'titulo' => $dados['tituloMaterial'], 'caminho' => $caminho, 'disco' => 'materiais',
            'mime' => $arquivo->getMimeType(), 'tamanho_bytes' => $arquivo->getSize(),
            'posicao' => ((int) $aula->materiais()->max('posicao')) + 1,
        ]);
        $this->reset('tituloMaterial', 'arquivoMaterial');
        Notification::make()->title('Material enviado')->success()->send();
    }

    public function renomearMaterial(int $materialId, string $titulo): void
    {
        $material = $this->materialAutorizado($materialId);
        $titulo = trim($titulo);

        if ($titulo !== '') {
            $material->update(['titulo' => Str::limit($titulo, 180, '')]);
        }
    }

    /** @param list<int> $ids */
    public function reordenarMateriais(array $ids): void
    {
        foreach ($ids as $posicao => $id) {
            $this->materialAutorizado($id)->update(['posicao' => $posicao]);
        }
    }

    public function excluirMaterial(int $materialId): void
    {
        $material = $this->materialAutorizado($materialId);
        Storage::disk($material->disco)->delete($material->caminho);
        $material->delete();
    }

    private function cursoAutorizado(): Curso
    {
        $curso = Curso::query()->findOrFail($this->cursoId);
        Gate::authorize('update', $curso);

        return $curso;
    }

    private function moduloAutorizado(int $moduloId): Modulo
    {
        return $this->cursoAutorizado()->modulos()->findOrFail($moduloId);
    }

    private function aulaAutorizada(int $aulaId): Aula
    {
        $aula = Aula::query()->with('modulo.curso')->findOrFail($aulaId);
        Gate::authorize('update', $aula);

        return $aula;
    }

    private function materialAutorizado(int $materialId): Material
    {
        $material = Material::query()->with('aula.modulo.curso')->findOrFail($materialId);
        Gate::authorize('update', $material);

        return $material;
    }

    private function limparAulaSelecionada(): void
    {
        $this->reset('aulaSelecionadaId', 'aulaTitulo', 'aulaDescricao', 'linkVideo', 'duracaoSegundos',
            'amostraGratuita', 'situacaoAula', 'mensagemMetadados');
        $this->situacaoAula = SituacaoAula::Rascunho->value;
    }
}
