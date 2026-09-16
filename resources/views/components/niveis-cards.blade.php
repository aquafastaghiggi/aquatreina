@php
    $niveis = config('premiacao.niveis');
    $estilos = [
        ['bg' => 'bg-blue-50', 'texto' => 'text-blue-700', 'estrela' => 'text-blue-500'],
        ['bg' => 'bg-teal-50', 'texto' => 'text-teal-700', 'estrela' => 'text-teal-500'],
        ['bg' => 'bg-amber-50', 'texto' => 'text-amber-700', 'estrela' => 'text-amber-500'],
        ['bg' => 'bg-orange-50', 'texto' => 'text-orange-700', 'estrela' => 'text-orange-500'],
        ['bg' => 'bg-pink-50', 'texto' => 'text-pink-700', 'estrela' => 'text-pink-500'],
        ['bg' => 'bg-purple-50', 'texto' => 'text-purple-700', 'estrela' => 'text-purple-500'],
    ];
@endphp

<div class="flex gap-4 overflow-x-auto pb-2 lg:grid lg:grid-cols-6 lg:overflow-visible">
    @foreach ($niveis as $indice => $nivel)
        @php($estilo = $estilos[$indice] ?? $estilos[0])
        <div class="flex w-48 shrink-0 flex-col gap-3 rounded-2xl {{ $estilo['bg'] }} p-5 lg:w-auto">
            <div class="flex gap-0.5 {{ $estilo['estrela'] }}">
                @for ($i = 0; $i <= $indice; $i++)
                    <x-heroicon-s-star class="h-4 w-4" />
                @endfor
            </div>
            <div>
                <p class="text-sm {{ $estilo['texto'] }}">Nível {{ $indice + 1 }}</p>
                <p class="font-semibold text-texto">{{ $nivel['nome'] }}</p>
            </div>
            <div>
                <p class="text-lg font-bold tabular-nums text-texto">{{ $nivel['faturamento'] }}</p>
                <p class="text-sm text-texto-2">{{ $nivel['reconhecimento'] }}</p>
            </div>
            @if ($indice === 0)
                <p class="mt-1 flex items-center gap-1 text-xs font-semibold {{ $estilo['texto'] }}">
                    <x-heroicon-o-arrow-up class="h-3.5 w-3.5" /> Comece aqui
                </p>
            @endif
        </div>
    @endforeach
</div>
