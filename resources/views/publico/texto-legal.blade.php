<x-layouts.publico :titulo="$tipo === 'termos' ? 'Termos de uso' : 'Privacidade'">
    <article class="prosa mx-auto max-w-3xl px-5 py-14">
        <h1>{{ $tipo === 'termos' ? 'Termos de uso' : 'Política de privacidade' }}</h1>
        @if ($texto)<p class="text-sm text-texto-3">Versão {{ $texto->versao }} · publicada em {{ $texto->publicado_em->format('d/m/Y') }}</p><div class="whitespace-pre-line">{{ $texto->conteudo }}</div>
        @else<div class="aviso">Texto jurídico provisório. Deve ser revisado antes da publicação.</div>@endif
    </article>
</x-layouts.publico>
