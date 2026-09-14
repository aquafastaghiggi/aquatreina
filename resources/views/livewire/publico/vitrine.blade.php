<div>
    <section class="mx-auto max-w-6xl px-5 py-16 sm:py-24">
        <p class="text-sm font-semibold uppercase tracking-[.18em] text-marca">Treinamento técnico Aquafast</p>
        <h1 class="mt-4 max-w-4xl text-balance text-4xl font-bold leading-tight sm:text-6xl">Conhecimento de produto para aplicar no trabalho.</h1>
        <p class="mt-6 max-w-2xl text-lg leading-8 text-texto-2">Cursos objetivos, materiais atualizados e acesso no seu ritmo — no celular ou no computador.</p>
        <div class="mt-8 flex flex-wrap gap-3">
            @guest<a href="{{ route('register') }}" class="botao-primario">Criar minha conta</a>@endguest
            <a href="{{ auth()->check() ? route('app.catalogo') : '#cursos' }}" class="botao-secundario">Ver cursos</a>
        </div>
    </section>

    <div id="cursos" class="mx-auto max-w-6xl space-y-14 px-5">
        @forelse ($categorias as $categoria)
            <section wire:key="categoria-{{ $categoria->id }}">
                <h2 class="text-2xl font-semibold">{{ $categoria->nome }}</h2>
                <div class="mt-5 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($categoria->cursos as $curso)
                        <x-curso-card wire:key="curso-{{ $curso->id }}" :curso="$curso" :capa-url="$apresentacao->urlCapa($curso)" />
                    @endforeach
                </div>
            </section>
        @empty
            <section class="rounded-lg border border-linha bg-superficie p-8 text-center">
                <h2 class="text-2xl font-semibold">Os primeiros treinamentos estão sendo preparados.</h2>
                <p class="mt-3 text-texto-2">Crie sua conta para estar pronto quando o catálogo abrir.</p>
            </section>
        @endforelse
    </div>
</div>
