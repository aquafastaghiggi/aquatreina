<x-layouts.publico :titulo="$tipo === 'termos' ? 'Termos de uso' : 'Privacidade'">
    <article class="prosa mx-auto w-full max-w-3xl px-4 py-10 sm:px-6 sm:py-14">
        <h1 class="text-3xl sm:text-4xl">{{ $tipo === 'termos' ? 'Termos de uso' : 'Política de privacidade' }}</h1>
        @if ($texto)<p class="text-sm text-texto-3">Versão {{ $texto->versao }} · publicada em {{ $texto->publicado_em->format('d/m/Y') }}</p><div class="mt-8 whitespace-pre-line break-words leading-7 text-texto-2">{{ $texto->conteudo }}</div>
        @else<div class="aviso">Texto jurídico provisório. Deve ser revisado antes da publicação.</div>@endif
    </article>
</x-layouts.publico>
