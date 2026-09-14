<article class="mx-auto max-w-6xl px-5 py-14">
        <div class="grid gap-10 lg:grid-cols-[1.15fr_.85fr] lg:items-start">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-marca">{{ $this->curso->categoria?->nome }}</p>
                <h1 class="mt-3 text-balance text-4xl font-bold sm:text-5xl">{{ $this->curso->titulo }}</h1>
                @if ($this->curso->subtitulo)<p class="mt-5 text-xl leading-8 text-texto-2">{{ $this->curso->subtitulo }}</p>@endif
                <div class="mt-6 flex flex-wrap gap-4 text-sm text-texto-3">
                    <span>{{ $this->curso->nivel->rotulo() }}</span>
                    <span>{{ $this->curso->minutos_estimados }} minutos</span>
                    <span>{{ $this->curso->total_aulas }} aulas</span>
                    <span>Com {{ $this->curso->responsavel->nome }}</span>
                </div>
                @if ($this->curso->descricao)<div class="prosa mt-8 text-texto-2">{!! $this->curso->descricao !!}</div>@endif
            </div>
            <aside class="rounded-lg border border-linha bg-superficie p-6">
                @if ($apresentacao->urlCapa($this->curso))
                    <img src="{{ $apresentacao->urlCapa($this->curso) }}" alt="" class="aspect-[16/10] w-full rounded-lg object-cover">
                @endif
                <div class="mt-6">
                    @if ($this->inscrito)
                        <a href="{{ route('app.curso', $this->curso) }}" class="botao-primario w-full">Ir para o curso</a>
                    @elseif (auth()->check())
                        <form method="POST" action="{{ route('app.inscrever', $this->curso) }}">@csrf<button class="botao-primario w-full" type="submit">Inscrever-me</button></form>
                    @else
                        <a href="{{ route('register') }}" class="botao-primario w-full">Criar conta para me inscrever</a>
                    @endif
                    <p class="mt-3 text-center text-sm text-texto-3">Acesso gratuito após liberação da conta.</p>
                </div>
            </aside>
        </div>

        <section class="mt-14 max-w-3xl">
            <h2 class="text-2xl font-semibold">Ementa</h2>
            <div class="mt-5 space-y-4">
                @foreach ($this->curso->modulos as $modulo)
                    <div wire:key="modulo-{{ $modulo->id }}" class="rounded-lg border border-linha bg-superficie p-5">
                        <h3 class="font-semibold">{{ $modulo->titulo }}</h3>
                        <ol class="mt-3 divide-y divide-linha">
                            @foreach ($modulo->aulas as $aula)
                                <li wire:key="aula-{{ $aula->id }}" class="flex items-center justify-between gap-4 py-3 text-sm text-texto-2">
                                    <span>{{ $aula->titulo }}</span>
                                    <span class="flex shrink-0 items-center gap-3 tabular-nums">
                                        {{ (int) ceil($aula->duracao_segundos / 60) }} min
                                        @if ($aula->amostra_gratuita)<a class="link" href="{{ route('cursos.amostra', [$this->curso, $aula]) }}">Ver amostra</a>@endif
                                    </span>
                                </li>
                            @endforeach
                        </ol>
                    </div>
                @endforeach
            </div>
        </section>
</article>
