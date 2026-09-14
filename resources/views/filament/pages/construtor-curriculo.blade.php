<x-filament-panels::page>
    @vite('resources/js/app.js')

    <div class="grid gap-6 lg:grid-cols-[minmax(300px,0.8fr)_minmax(420px,1.2fr)]">
        <x-filament::section>
            <x-slot name="heading">Módulos e aulas</x-slot>

            <form wire:submit="criarModulo" class="mb-5 flex gap-2">
                <x-filament::input.wrapper class="flex-1">
                    <x-filament::input wire:model="novoModuloTitulo" placeholder="Título do novo módulo" />
                </x-filament::input.wrapper>
                <x-filament::button type="submit">Criar módulo</x-filament::button>
            </form>

            <div
                x-data
                x-init="const iniciar = () => window.iniciarOrdenacaoCurriculo($el, (ordem) => $wire.reordenar(ordem)); window.iniciarOrdenacaoCurriculo ? iniciar() : window.addEventListener('curriculo-js-pronto', iniciar, { once: true })"
                data-curriculo
                class="space-y-4"
            >
                @forelse ($this->curso->modulos as $modulo)
                    <section wire:key="modulo-{{ $modulo->id }}" data-modulo-id="{{ $modulo->id }}" class="rounded-xl border border-gray-200 p-3 dark:border-white/10">
                        <div class="flex items-center gap-2">
                            <button type="button" class="modulo-alca cursor-grab text-gray-400" aria-label="Arrastar módulo">⋮⋮</button>
                            <input
                                value="{{ $modulo->titulo }}"
                                wire:change="renomearModulo({{ $modulo->id }}, $event.target.value)"
                                class="min-w-0 flex-1 rounded-lg border-gray-300 bg-transparent font-semibold dark:border-white/10"
                            />
                            <x-filament::icon-button wire:click="criarAula({{ $modulo->id }})" icon="heroicon-o-plus" label="Criar aula" />
                            <x-filament::icon-button wire:click="excluirModulo({{ $modulo->id }})" wire:confirm="Excluir o módulo e suas aulas?" icon="heroicon-o-trash" color="danger" label="Excluir módulo" />
                        </div>

                        <div data-aulas class="mt-3 space-y-2">
                            @foreach ($modulo->aulas as $aula)
                                <div wire:key="aula-{{ $aula->id }}" data-aula-id="{{ $aula->id }}" class="flex items-center gap-2 rounded-lg bg-gray-50 p-2 dark:bg-white/5">
                                    <button type="button" class="aula-alca cursor-grab text-gray-400" aria-label="Arrastar aula">⋮⋮</button>
                                    <button type="button" wire:click="selecionarAula({{ $aula->id }})" class="min-w-0 flex-1 truncate text-left {{ $aulaSelecionadaId === $aula->id ? 'font-bold text-primary-600' : '' }}">
                                        {{ $aula->titulo }}
                                    </button>
                                    <x-filament::icon-button wire:click="duplicarAula({{ $aula->id }})" icon="heroicon-o-square-2-stack" label="Duplicar aula" />
                                    <x-filament::icon-button wire:click="excluirAula({{ $aula->id }})" wire:confirm="Excluir esta aula?" icon="heroicon-o-trash" color="danger" label="Excluir aula" />
                                </div>
                            @endforeach
                        </div>
                    </section>
                @empty
                    <p class="text-sm text-gray-500">Crie o primeiro módulo para começar.</p>
                @endforelse
            </div>
        </x-filament::section>

        <div class="space-y-6">
            <x-filament::section>
                <x-slot name="heading">Edição da aula</x-slot>

                @if ($aulaSelecionadaId)
                    <form wire:submit="salvarAula" class="space-y-4">
                        <x-filament::input.wrapper><x-filament::input wire:model="aulaTitulo" placeholder="Título" /></x-filament::input.wrapper>
                        <textarea wire:model="aulaDescricao" rows="5" placeholder="Descrição" class="w-full rounded-lg border-gray-300 dark:border-white/10 dark:bg-white/5"></textarea>
                        <x-filament::input.wrapper><x-filament::input wire:model="linkVideo" placeholder="Cole o link do YouTube" /></x-filament::input.wrapper>
                        @error('link_video') <p class="text-sm text-danger-600">{{ $message }}</p> @enderror

                        @if ($mensagemMetadados)
                            <p class="rounded-lg bg-success-50 p-3 text-sm text-success-700 dark:bg-success-500/10">{{ $mensagemMetadados }}</p>
                        @endif

                        <label class="block text-sm font-medium">Duração em segundos
                            <x-filament::input.wrapper><x-filament::input type="number" min="0" wire:model="duracaoSegundos" /></x-filament::input.wrapper>
                        </label>
                        @if ($duracaoSegundos === 0)
                            <p class="rounded-lg bg-warning-50 p-3 text-sm text-warning-700 dark:bg-warning-500/10">Sem duração, a conclusão automática não funcionará.</p>
                        @endif

                        <div class="grid gap-3 sm:grid-cols-2">
                            <label class="flex items-center gap-2"><input type="checkbox" wire:model="amostraGratuita"> Amostra gratuita</label>
                            <select wire:model="situacaoAula" class="rounded-lg border-gray-300 dark:border-white/10 dark:bg-white/5">
                                <option value="rascunho">Rascunho</option>
                                <option value="publicada">Publicada</option>
                            </select>
                        </div>
                        <x-filament::button type="submit">Salvar aula</x-filament::button>
                    </form>
                @else
                    <p class="text-sm text-gray-500">Selecione ou crie uma aula.</p>
                @endif
            </x-filament::section>

            @if ($aulaSelecionadaId)
                <x-filament::section>
                    <x-slot name="heading">Materiais da aula</x-slot>
                    <form wire:submit="enviarMaterial" class="space-y-3">
                        <x-filament::input.wrapper><x-filament::input wire:model="tituloMaterial" placeholder="Título do material" /></x-filament::input.wrapper>
                        <input type="file" wire:model="arquivoMaterial" accept=".pdf,.xlsx,.docx,.pptx,.png,.jpg,.jpeg,.zip" class="block w-full text-sm" />
                        @error('arquivoMaterial') <p class="text-sm text-danger-600">{{ $message }}</p> @enderror
                        <x-filament::button type="submit">Enviar material</x-filament::button>
                    </form>

                    <div class="mt-5 space-y-2" x-data x-init="const iniciar = () => window.iniciarOrdenacaoMateriais($el, (ids) => $wire.reordenarMateriais(ids)); window.iniciarOrdenacaoMateriais ? iniciar() : window.addEventListener('curriculo-js-pronto', iniciar, { once: true })" data-materiais>
                        @foreach ($this->curso->modulos->flatMap->aulas->firstWhere('id', $aulaSelecionadaId)?->materiais ?? [] as $material)
                            <div wire:key="material-{{ $material->id }}" data-material-id="{{ $material->id }}" class="flex items-center gap-2 rounded-lg bg-gray-50 p-2 dark:bg-white/5">
                                <button type="button" class="material-alca cursor-grab">⋮⋮</button>
                                <input value="{{ $material->titulo }}" wire:change="renomearMaterial({{ $material->id }}, $event.target.value)" class="min-w-0 flex-1 bg-transparent" />
                                <x-filament::icon-button wire:click="excluirMaterial({{ $material->id }})" wire:confirm="Excluir este material?" icon="heroicon-o-trash" color="danger" label="Excluir material" />
                            </div>
                        @endforeach
                    </div>
                </x-filament::section>
            @endif
        </div>
    </div>
</x-filament-panels::page>
