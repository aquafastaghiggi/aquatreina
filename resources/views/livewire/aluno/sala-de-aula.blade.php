@php
    $concluida = in_array($this->aula->id, $this->aulasConcluidas, true);
    $progressoAtual = $this->progressoAtual;
    $concluidas = count(array_intersect(
        $this->aulasConcluidas,
        $this->curso->modulos->flatMap->aulas->pluck('id')->all(),
    ));
@endphp

<div
    data-player-aula
    data-aula-id="{{ $this->aula->id }}"
    data-endpoint="{{ route('app.progresso') }}"
    data-intervalo="{{ config('treina.intervalo_ping') }}"
    data-posicao="{{ $progressoAtual?->posicao_maxima ?? 0 }}"
    data-concluida="{{ $concluida ? '1' : '0' }}"
>
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <a href="{{ route('app.painel') }}" class="link text-sm">← Minha área</a>
            <p class="mt-3 text-sm font-semibold uppercase tracking-wide text-marca">{{ $this->curso->titulo }}</p>
        </div>
        <a href="{{ route('app.catalogo') }}" class="link text-sm">Catálogo</a>
    </div>

    <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_320px] lg:items-start">
        <main class="min-w-0">
            <div wire:ignore class="relative aspect-video overflow-hidden rounded-lg border border-linha bg-black">
                <iframe
                    id="player-aula-{{ $this->aula->id }}"
                    class="h-full w-full"
                    src="{{ $urlEmbed }}"
                    title="{{ $this->aula->titulo }}"
                    allow="accelerometer; autoplay; encrypted-media; picture-in-picture"
                    allowfullscreen
                ></iframe>
            </div>

            @if (($progressoAtual?->posicao_maxima ?? 0) > 0 && ! $concluida)
                <button type="button" data-retomar class="botao-secundario mt-4 text-sm">
                    Continuar de {{ gmdate('i:s', $progressoAtual->posicao_maxima) }}
                </button>
            @endif

            <div class="mt-7 flex flex-wrap items-start justify-between gap-5">
                <div>
                    <p class="text-sm text-texto-3">{{ $this->aula->modulo->titulo }}</p>
                    <h1 class="mt-1 text-balance text-3xl font-semibold">{{ $this->aula->titulo }}</h1>
                </div>
                <div class="flex flex-wrap gap-3">
                    @unless ($concluida)
                        <button wire:click="concluirManualmente" wire:loading.attr="disabled" class="botao-secundario" type="button">
                            Marcar como concluída
                        </button>
                    @endunless
                    @if ($this->proximaAula)
                        <a
                            href="{{ route('app.aula', [$this->curso, $this->proximaAula]) }}"
                            class="{{ $concluida ? 'botao-primario' : 'botao-secundario pointer-events-none opacity-50' }}"
                            @unless ($concluida) aria-disabled="true" tabindex="-1" @endunless
                        >Próxima aula</a>
                    @endif
                </div>
            </div>

            @if ($this->aula->descricao || $this->aula->materiais->isNotEmpty())
                <div x-data="{ aba: '{{ $this->aula->descricao ? 'sobre' : 'materiais' }}' }" class="mt-10">
                    <div class="flex gap-5 border-b border-linha" role="tablist" aria-label="Conteúdo da aula">
                        @if ($this->aula->descricao)
                            <button type="button" @click="aba = 'sobre'" :class="aba === 'sobre' ? 'border-marca text-texto' : 'border-transparent text-texto-3'" class="border-b-2 px-1 py-3" role="tab">Sobre a aula</button>
                        @endif
                        @if ($this->aula->materiais->isNotEmpty())
                            <button type="button" @click="aba = 'materiais'" :class="aba === 'materiais' ? 'border-marca text-texto' : 'border-transparent text-texto-3'" class="border-b-2 px-1 py-3" role="tab">Materiais</button>
                        @endif
                    </div>
                    @if ($this->aula->descricao)
                        <div x-show="aba === 'sobre'" class="prosa py-6">{!! $this->aula->descricao !!}</div>
                    @endif
                    @if ($this->aula->materiais->isNotEmpty())
                        <div x-show="aba === 'materiais'" class="space-y-3 py-6">
                            @foreach ($this->aula->materiais as $material)
                                <div wire:key="material-{{ $material->id }}" class="rounded-lg border border-linha bg-superficie p-4">
                                    <p class="font-medium">{{ $material->titulo }}</p>
                                    <p class="mt-1 text-sm text-texto-3">Download disponível na próxima etapa.</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif
        </main>

        <aside class="min-w-0 rounded-lg border border-linha bg-superficie p-5">
            <div class="mb-5">
                <div class="flex items-center justify-between gap-3 text-sm">
                    <span>Progresso</span>
                    <strong class="tabular-nums">{{ $this->percentualCurso }}%</strong>
                </div>
                <div class="mt-2 h-2 overflow-hidden rounded-full bg-linha">
                    <div class="h-full bg-marca transition-all" style="width: {{ $this->percentualCurso }}%"></div>
                </div>
                <p class="mt-2 text-sm text-texto-3">{{ $concluidas }} de {{ $totalAulas }} aulas</p>
            </div>

            <details class="group" open>
                <summary class="cursor-pointer list-none font-semibold lg:hidden">Índice do curso</summary>
                <nav class="mt-4 space-y-3 lg:mt-0" aria-label="Aulas do curso">
                    @foreach ($this->curso->modulos as $modulo)
                        <details wire:key="modulo-{{ $modulo->id }}" class="rounded-md border border-linha" @if ($modulo->id === $this->aula->modulo_id) open @endif>
                            <summary class="cursor-pointer px-3 py-3 font-medium">{{ $modulo->titulo }}</summary>
                            <ol class="border-t border-linha">
                                @foreach ($modulo->aulas as $aula)
                                    <li wire:key="indice-aula-{{ $aula->id }}">
                                        <a href="{{ route('app.aula', [$this->curso, $aula]) }}" class="flex items-center gap-3 px-3 py-3 text-sm {{ $aula->id === $this->aulaId ? 'bg-marca-suave text-texto' : 'text-texto-2 hover:bg-superficie-2' }}">
                                            <span class="w-4 text-center text-sucesso" aria-hidden="true">{{ in_array($aula->id, $this->aulasConcluidas, true) ? '✓' : '○' }}</span>
                                            <span class="min-w-0 flex-1">{{ $aula->titulo }}</span>
                                            <span class="shrink-0 tabular-nums text-texto-3">{{ gmdate('i:s', $aula->duracao_segundos) }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ol>
                        </details>
                    @endforeach
                </nav>
            </details>
        </aside>
    </div>
</div>
