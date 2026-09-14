<x-layouts.publico :titulo="$aula->titulo">
    <article class="mx-auto max-w-5xl px-5 py-12">
        <a href="{{ route('cursos.mostrar', $curso) }}" class="link">← Voltar para {{ $curso->titulo }}</a>
        <p class="mt-8 text-sm font-semibold uppercase tracking-wide text-marca">Aula de amostra</p>
        <h1 class="mt-2 text-balance text-3xl font-semibold">{{ $aula->titulo }}</h1>
        <div class="mt-7 aspect-video overflow-hidden rounded-lg border border-linha bg-black">
            <iframe class="h-full w-full" src="{{ $urlEmbed }}" title="Amostra: {{ $aula->titulo }}" allow="accelerometer; autoplay; encrypted-media; picture-in-picture" allowfullscreen></iframe>
        </div>
    </article>
</x-layouts.publico>
