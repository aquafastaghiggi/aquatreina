<x-filament-panels::page>
    <div class="flex flex-wrap gap-2">
        @foreach (['cursos' => 'Por curso', 'alunos' => 'Por aluno', 'organizacoes' => 'Por organização'] as $valor => $rotulo)
            <x-filament::button wire:click="mudarAba('{{ $valor }}')" :color="$aba === $valor ? 'primary' : 'gray'">{{ $rotulo }}</x-filament::button>
        @endforeach
        <x-filament::button wire:click="exportar" icon="heroicon-o-arrow-down-tray" class="ml-auto">Exportar XLSX</x-filament::button>
    </div>

    @if ($aba === 'alunos')
        <div class="grid gap-4 md:grid-cols-4">
            <x-filament::input.wrapper><x-filament::input wire:model.live.debounce.400ms="empresa" placeholder="Empresa" /></x-filament::input.wrapper>
            <x-filament::input.wrapper><x-filament::input.select wire:model.live="situacao"><option value="">Todas as situações</option><option value="ativo">Ativo</option><option value="pendente">Pendente</option><option value="bloqueado">Bloqueado</option></x-filament::input.select></x-filament::input.wrapper>
            <x-filament::input.wrapper><x-filament::input type="date" wire:model.live="inicio" /></x-filament::input.wrapper>
            <x-filament::input.wrapper><x-filament::input type="date" wire:model.live="fim" /></x-filament::input.wrapper>
        </div>
    @endif

    <div class="overflow-x-auto rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <table class="w-full text-left text-sm">
            <thead class="border-b dark:border-white/10"><tr>
                @if ($aba === 'cursos')<th class="p-4">Curso</th><th>Matriculados</th><th>Concluídos</th><th>Progresso médio</th><th>Aula de abandono</th>
                @elseif ($aba === 'alunos')<th class="p-4">Aluno</th><th>Empresa</th><th>Situação</th><th>Cursos</th><th>Progresso médio</th><th>Última atividade</th>
                @else<th class="p-4">Organização</th><th>Matriculados</th><th>Concluídos</th>@endif
            </tr></thead>
            <tbody class="divide-y dark:divide-white/10">
                @forelse ($this->linhas as $linha)<tr>
                    @if ($aba === 'cursos')<td class="p-4">{{ $linha->titulo }}</td><td>{{ $linha->matriculados }}</td><td>{{ $linha->concluidos }}</td><td>{{ $linha->percentual_medio }}%</td><td>{{ $linha->aula_maior_abandono ?? '—' }}</td>
                    @elseif ($aba === 'alunos')<td class="p-4"><strong>{{ $linha->nome }}</strong><br>{{ $linha->email }}</td><td>{{ $linha->empresa ?? '—' }}</td><td>{{ $linha->situacao }}</td><td>{{ $linha->cursos }}</td><td>{{ $linha->percentual_medio }}%</td><td>{{ $linha->ultima_atividade ?? '—' }}</td>
                    @else<td class="p-4">{{ $linha->nome }}</td><td>{{ $linha->matriculados }}</td><td>{{ $linha->concluidos }}</td>@endif
                </tr>@empty<tr><td class="p-4" colspan="7">Nenhum registro.</td></tr>@endforelse
            </tbody>
        </table>
    </div>
    {{ $this->linhas->links() }}
</x-filament-panels::page>
