@php($niveis = config('premiacao.niveis'))

<div class="overflow-hidden rounded-lg border border-linha bg-superficie">
    <table class="hidden w-full text-left lg:table">
        <thead class="border-b border-linha text-xs font-semibold uppercase tracking-wide text-texto-3">
            <tr>
                <th class="px-5 py-4">Nível</th>
                <th class="px-5 py-4">Faturamento</th>
                <th class="px-5 py-4">Reconhecimento</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-linha">
            @foreach ($niveis as $indice => $nivel)
                <tr>
                    <td class="px-5 py-4 font-semibold text-marca">Nível {{ $indice + 1 }} — {{ $nivel['nome'] }}</td>
                    <td class="px-5 py-4 tabular-nums text-texto-2">{{ $nivel['faturamento'] }}</td>
                    <td class="px-5 py-4 text-texto">{{ $nivel['reconhecimento'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <ul class="divide-y divide-linha lg:hidden">
        @foreach ($niveis as $indice => $nivel)
            <li class="flex items-center justify-between gap-4 px-5 py-4">
                <div>
                    <p class="font-semibold text-marca">Nível {{ $indice + 1 }} — {{ $nivel['nome'] }}</p>
                    <p class="mt-1 text-sm tabular-nums text-texto-2">{{ $nivel['faturamento'] }}</p>
                </div>
                <p class="text-right text-sm text-texto">{{ $nivel['reconhecimento'] }}</p>
            </li>
        @endforeach
    </ul>
</div>
