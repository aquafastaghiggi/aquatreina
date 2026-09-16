@props(['curso', 'capaUrl' => null, 'inscrito' => false, 'percentual' => null])

<article class="overflow-hidden rounded-lg border border-linha bg-superficie">
    <a href="{{ $inscrito ? route('app.curso', $curso) : route('cursos.mostrar', $curso) }}" class="group block focus-ring">
        <div class="aspect-[16/10] overflow-hidden bg-marca-suave">
            @if ($capaUrl)
                <img src="{{ $capaUrl }}" alt="" class="h-full w-full object-cover transition duration-200 group-hover:scale-[1.02] motion-reduce:transition-none" loading="lazy">
            @else
                <div class="flex h-full items-center justify-center text-sm text-texto-3">Universidade Aquafast</div>
            @endif
        </div>
        <div class="p-5">
            <div class="flex items-center justify-between gap-3 text-xs font-semibold uppercase tracking-wide text-marca">
                <span>{{ $curso->categoria?->nome ?? 'Produto Aquafast' }}</span>
                @if ($inscrito)<span class="text-sucesso">Inscrito</span>@endif
            </div>
            <h3 class="mt-3 line-clamp-2 min-h-12 text-balance text-lg font-semibold text-texto">{{ $curso->titulo }}</h3>
            <p class="mt-3 text-sm text-texto-3">{{ $curso->minutos_estimados }} min · {{ $curso->nivel->rotulo() }}</p>
            @if ($percentual !== null)
                <div class="mt-4">
                    <div class="mb-2 flex justify-between text-xs text-texto-2"><span>Progresso</span><span class="tabular-nums">{{ $percentual }}%</span></div>
                    <div class="h-[5px] overflow-hidden rounded-full bg-linha"><div class="h-full bg-marca" style="width: {{ $percentual }}%"></div></div>
                </div>
            @endif
        </div>
    </a>
</article>
