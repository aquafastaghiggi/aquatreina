<x-filament-panels::page>
    <div class="flex justify-end"><a class="fi-link" href="{{ route('admin.usuarios.modelo') }}">Baixar modelo CSV</a></div>
    <form wire:submit="previsualizar" class="space-y-4">
        <input type="file" wire:model="arquivo" accept=".csv,text/csv" />
        @error('arquivo')<p class="text-danger-600">{{ $message }}</p>@enderror
        <x-filament::button type="submit">Pré-visualizar</x-filament::button>
    </form>
    @if ($linhas !== [])
        <div class="overflow-x-auto rounded-xl bg-white shadow-sm dark:bg-gray-900"><table class="w-full text-sm">
            <thead><tr><th class="p-3">Linha</th><th>Nome</th><th>E-mail</th><th>Empresa</th><th>Organização</th><th>Cursos</th><th>Validação</th></tr></thead>
            <tbody>@foreach ($linhas as $linha)<tr class="border-t {{ $linha['erros'] !== [] ? 'bg-danger-50 dark:bg-danger-950' : '' }}">
                <td class="p-3">{{ $linha['linha'] }}</td><td>{{ $linha['dados']['nome'] }}</td><td>{{ $linha['dados']['email'] }}</td><td>{{ $linha['dados']['empresa'] }}</td><td>{{ $linha['dados']['organizacao'] }}</td><td>{{ $linha['dados']['cursos'] }}</td><td>{{ $linha['erros'] === [] ? 'Válida' : implode(' ', $linha['erros']) }}</td>
            </tr>@endforeach</tbody>
        </table></div>
        <x-filament::button wire:click="confirmar" color="success">Confirmar importação</x-filament::button>
    @endif
</x-filament-panels::page>
