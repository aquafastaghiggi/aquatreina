<div>
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div><p class="text-sm text-marca">Explore</p><h1 class="mt-1 text-3xl font-semibold">Catálogo de treinamentos</h1></div>
        <a href="{{ route('app.painel') }}" class="link">Minha área</a>
    </div>

    <details class="mt-7 rounded-lg border border-linha bg-superficie p-4 lg:hidden">
        <summary class="cursor-pointer font-semibold">Busca e filtros</summary>
        <div class="mt-4 grid gap-3">
            <label class="campo">Buscar<input type="search" wire:model.live.debounce.350ms="busca" placeholder="Título ou assunto"></label>
            <select wire:model.live="categoria" class="focus-ring rounded-md border border-linha bg-superficie-2 px-4 py-3"><option value="">Todas as categorias</option>@foreach ($this->categorias as $item)<option value="{{ $item->slug }}">{{ $item->nome }}</option>@endforeach</select>
            <select wire:model.live="nivel" class="focus-ring rounded-md border border-linha bg-superficie-2 px-4 py-3"><option value="">Todos os níveis</option>@foreach ($niveis as $item)<option value="{{ $item->value }}">{{ $item->rotulo() }}</option>@endforeach</select>
        </div>
    </details>

    <div class="mt-7 hidden grid-cols-[1fr_240px_200px] gap-3 lg:grid">
        <label class="campo">Buscar<input type="search" wire:model.live.debounce.350ms="busca" placeholder="Título ou assunto"></label>
        <label class="campo">Categoria<select wire:model.live="categoria" class="focus-ring rounded-md border border-linha bg-superficie-2 px-4 py-3"><option value="">Todas</option>@foreach ($this->categorias as $item)<option value="{{ $item->slug }}">{{ $item->nome }}</option>@endforeach</select></label>
        <label class="campo">Nível<select wire:model.live="nivel" class="focus-ring rounded-md border border-linha bg-superficie-2 px-4 py-3"><option value="">Todos</option>@foreach ($niveis as $item)<option value="{{ $item->value }}">{{ $item->rotulo() }}</option>@endforeach</select></label>
    </div>

    <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($this->cursos as $curso)
            <x-curso-card wire:key="curso-{{ $curso->id }}" :curso="$curso" :capa-url="$apresentacao->urlCapa($curso)" :inscrito="(bool) $curso->inscrito" />
        @endforeach
    </div>

    @if ($this->cursos->isEmpty())
        <div class="mt-8 rounded-lg border border-linha bg-superficie p-8 text-center">
            <p class="text-lg font-semibold">Nenhum curso encontrado com esses filtros.</p>
            <button type="button" wire:click="limparFiltros" class="botao-secundario mt-5">Limpar filtros</button>
        </div>
    @endif

    <div class="mt-8">{{ $this->cursos->links() }}</div>
</div>
