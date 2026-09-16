@props(['compacta' => false])

@php($passos = ['Dor', 'Solução', 'Uso', 'Resultado', 'Sacolinha'])

@if ($compacta)
    <p class="flex flex-wrap items-center gap-2 text-sm font-semibold uppercase tracking-wide text-texto-2" aria-label="Fórmula de conteúdo Aquafast">
        @foreach ($passos as $passo)
            <span>{{ $passo }}</span>
            @if (! $loop->last)
                <span class="text-marca" aria-hidden="true">→</span>
            @endif
        @endforeach
    </p>
@else
    <div class="flex flex-col gap-3 rounded-lg border border-linha bg-superficie p-5 lg:flex-row lg:items-center lg:justify-between lg:gap-2" role="list" aria-label="Fórmula de conteúdo Aquafast">
        @foreach ($passos as $indice => $passo)
            <div class="flex items-center gap-3 lg:flex-1" role="listitem">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-marca text-sm font-bold tabular-nums text-fundo">{{ $indice + 1 }}</span>
                <span class="font-semibold uppercase tracking-wide text-texto">{{ $passo }}</span>
            </div>
            @if (! $loop->last)
                <span class="hidden text-texto-3 lg:block" aria-hidden="true">→</span>
            @endif
        @endforeach
    </div>
@endif
