<div>
    @if (session('sucesso'))<div class="sucesso mb-6">{{ session('sucesso') }}</div>@endif
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div><p class="text-sm text-marca">Olá, {{ auth()->user()->nome }}</p><h1 class="mt-1 text-3xl font-semibold">Sua área de treinamento</h1></div>
        <div class="flex gap-4"><a href="{{ route('app.catalogo') }}" class="link">Catálogo</a><a href="{{ route('app.perfil') }}" class="link">Perfil</a></div>
    </div>

    @if ($emAndamento->isEmpty() && $concluidas->isEmpty())
        <section class="mt-8 rounded-lg border border-linha bg-superficie p-8 text-center">
            <h2 class="text-2xl font-semibold">Você ainda não está em nenhum treinamento.</h2>
            <p class="mt-3 text-texto-2">Escolha um curso e comece quando quiser.</p>
            <a href="{{ route('app.catalogo') }}" class="botao-primario mt-6">Explorar catálogo</a>
        </section>
    @else
        @if ($emAndamento->isNotEmpty())
            @php($recente = $emAndamento->first())
            <section class="mt-8 rounded-lg border border-marca/50 bg-marca-suave p-7 shadow-lg shadow-black/10">
                <p class="text-sm font-semibold text-marca">Continue de onde parou</p>
                <div class="mt-2 flex flex-wrap items-end justify-between gap-4">
                    <div><h2 class="text-2xl font-semibold">{{ $recente->curso->titulo }}</h2><p class="mt-2 text-texto-2">{{ $recente->percentual_progresso }}% concluído</p></div>
                    <a href="{{ route('app.curso', $recente->curso) }}" class="botao-primario">Continuar</a>
                </div>
            </section>

            <section class="mt-12"><h2 class="text-2xl font-semibold">Em andamento</h2><div class="mt-5 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($emAndamento as $matricula)<x-curso-card wire:key="matricula-{{ $matricula->id }}" :curso="$matricula->curso" :capa-url="$apresentacao->urlCapa($matricula->curso)" :inscrito="true" :percentual="$matricula->percentual_progresso" />@endforeach
            </div></section>
        @endif

        @if ($concluidas->isNotEmpty())
            <details class="mt-12 rounded-lg border border-linha bg-superficie p-5"><summary class="cursor-pointer text-xl font-semibold">Concluídos ({{ $concluidas->count() }})</summary><div class="mt-5 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($concluidas as $matricula)<x-curso-card wire:key="concluida-{{ $matricula->id }}" :curso="$matricula->curso" :capa-url="$apresentacao->urlCapa($matricula->curso)" :inscrito="true" :percentual="100" />@endforeach
            </div></details>
        @endif
    @endif

    @if ($this->sugestoes->isNotEmpty())
        <section class="mt-12"><h2 class="text-2xl font-semibold">Outros treinamentos</h2><div class="mt-5 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($this->sugestoes as $curso)<x-curso-card wire:key="sugestao-{{ $curso->id }}" :curso="$curso" :capa-url="$apresentacao->urlCapa($curso)" />@endforeach
        </div></section>
    @endif
</div>
