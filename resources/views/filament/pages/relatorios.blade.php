<x-filament-panels::page>
    <div class="flex flex-wrap items-center gap-2">
        @foreach (['cursos' => 'Por curso', 'alunos' => 'Por aluno', 'organizacoes' => 'Por organização'] as $valor => $rotulo)
            <x-filament::button wire:click="mudarAba('{{ $valor }}')" :color="$aba === $valor ? 'primary' : 'gray'" size="sm">{{ $rotulo }}</x-filament::button>
        @endforeach
        <x-filament::button wire:click="exportar" icon="heroicon-o-arrow-down-tray" color="gray" size="sm" class="ms-auto">Exportar XLSX</x-filament::button>
    </div>

    @if ($aba === 'alunos')
        <div class="grid gap-4 rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 md:grid-cols-4">
            <x-filament::input.wrapper>
                <x-filament::input wire:model.live.debounce.400ms="empresa" placeholder="Empresa" />
            </x-filament::input.wrapper>
            <x-filament::input.wrapper>
                <x-filament::input.select wire:model.live="situacao">
                    <option value="">Todas as situações</option>
                    <option value="ativo">Ativo</option>
                    <option value="pendente">Pendente</option>
                    <option value="bloqueado">Bloqueado</option>
                </x-filament::input.select>
            </x-filament::input.wrapper>
            <x-filament::input.wrapper suffix-icon="heroicon-o-calendar">
                <x-filament::input type="date" wire:model.live="inicio" />
            </x-filament::input.wrapper>
            <x-filament::input.wrapper suffix-icon="heroicon-o-calendar">
                <x-filament::input type="date" wire:model.live="fim" />
            </x-filament::input.wrapper>
        </div>
    @endif

    <div class="overflow-x-auto rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 dark:bg-white/5">
                <tr class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                    @if ($aba === 'cursos')
                        <th class="px-4 py-3">Curso</th>
                        <th class="px-4 py-3 text-right">Matriculados</th>
                        <th class="px-4 py-3 text-right">Concluídos</th>
                        <th class="px-4 py-3 text-right">Progresso médio</th>
                        <th class="px-4 py-3">Aula de abandono</th>
                    @elseif ($aba === 'alunos')
                        <th class="px-4 py-3">Aluno</th>
                        <th class="px-4 py-3">Empresa</th>
                        <th class="px-4 py-3">Situação</th>
                        <th class="px-4 py-3 text-right">Cursos</th>
                        <th class="px-4 py-3 text-right">Progresso médio</th>
                        <th class="px-4 py-3">Última atividade</th>
                    @else
                        <th class="px-4 py-3">Organização</th>
                        <th class="px-4 py-3 text-right">Matriculados</th>
                        <th class="px-4 py-3 text-right">Concluídos</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-white/10">
                @forelse ($this->linhas as $linha)
                    <tr class="hover:bg-gray-50 dark:hover:bg-white/5">
                        @if ($aba === 'cursos')
                            <td class="px-4 py-3 font-medium text-gray-950 dark:text-white">{{ $linha->titulo }}</td>
                            <td class="px-4 py-3 text-right tabular-nums">{{ $linha->matriculados }}</td>
                            <td class="px-4 py-3 text-right tabular-nums">{{ $linha->concluidos }}</td>
                            <td class="px-4 py-3 text-right tabular-nums">{{ $linha->percentual_medio }}%</td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $linha->aula_maior_abandono ?? '—' }}</td>
                        @elseif ($aba === 'alunos')
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-950 dark:text-white">{{ $linha->nome }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $linha->email }}</div>
                            </td>
                            <td class="px-4 py-3">{{ $linha->empresa ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <x-filament::badge :color="match ($linha->situacao) {
                                    \App\Enums\SituacaoUsuario::Ativo => 'success',
                                    \App\Enums\SituacaoUsuario::Pendente => 'warning',
                                    \App\Enums\SituacaoUsuario::Bloqueado => 'danger',
                                    default => 'gray',
                                }">
                                    {{ $linha->situacao->rotulo() }}
                                </x-filament::badge>
                            </td>
                            <td class="px-4 py-3 text-right tabular-nums">{{ $linha->cursos }}</td>
                            <td class="px-4 py-3 text-right tabular-nums">{{ $linha->percentual_medio }}%</td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $linha->ultima_atividade ?? '—' }}</td>
                        @else
                            <td class="px-4 py-3 font-medium text-gray-950 dark:text-white">{{ $linha->nome }}</td>
                            <td class="px-4 py-3 text-right tabular-nums">{{ $linha->matriculados }}</td>
                            <td class="px-4 py-3 text-right tabular-nums">{{ $linha->concluidos }}</td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td class="px-4 py-10 text-center text-gray-500 dark:text-gray-400" colspan="7">Nenhum registro encontrado para os filtros atuais.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $this->linhas->links() }}
</x-filament-panels::page>
