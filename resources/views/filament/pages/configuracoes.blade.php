<x-filament-panels::page>
    <form wire:submit="salvar" class="space-y-6">
        <x-filament::section heading="Operação">
            <div class="grid gap-5 md:grid-cols-2">
                <p class="rounded-lg bg-gray-50 p-4 text-sm dark:bg-white/5">Todo novo cadastro aguarda aprovação obrigatória de um administrador.</p>
                <label>Conclusão automática (%)<x-filament::input.wrapper><x-filament::input type="number" wire:model="percentualConclusao" /></x-filament::input.wrapper></label>
                <label>Intervalo de ping (segundos)<x-filament::input.wrapper><x-filament::input type="number" wire:model="intervaloPing" /></x-filament::input.wrapper></label>
                <label>Texto de boas-vindas<x-filament::input.wrapper><x-filament::input wire:model="textoBoasVindas" /></x-filament::input.wrapper></label>
            </div>
        </x-filament::section>
        <x-filament::section heading="Textos legais versionados">
            <div class="space-y-5">
                <label>Termos de uso<textarea wire:model="termos" rows="10" class="fi-input w-full rounded-lg"></textarea></label>
                <label>Política de privacidade<textarea wire:model="privacidade" rows="10" class="fi-input w-full rounded-lg"></textarea></label>
            </div>
        </x-filament::section>
        <x-filament::button type="submit">Salvar configurações</x-filament::button>
    </form>
</x-filament-panels::page>
