<div class="overflow-x-hidden">
    {{-- Hero --}}
    <section class="relative">
        <div class="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true">
            <div class="absolute -top-24 -right-24 h-96 w-96 rounded-full bg-blue-100 blur-3xl"></div>
            <div class="absolute top-40 -left-24 h-72 w-72 rounded-full bg-superficie blur-3xl"></div>
        </div>

        <div class="relative mx-auto grid max-w-6xl gap-12 px-5 py-16 sm:py-24 lg:grid-cols-[1.05fr_0.95fr] lg:items-center">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[.18em] text-marca">Curso 100% gratuito · TikTok Shop</p>
                <h1 class="mt-4 text-balance text-5xl font-extrabold leading-[1.05] tracking-tight sm:text-6xl">
                    Transforme<br>seu conteúdo<br><span class="text-marca">em renda.</span>
                </h1>
                <p class="mt-6 max-w-lg text-lg leading-8 text-texto-2">Aprenda a criar conteúdos para a Aquafast, divulgar nossos kits no TikTok Shop e ganhar comissão por cada venda.</p>

                <ul class="mt-6 flex flex-wrap gap-x-6 gap-y-2 text-sm font-medium text-texto">
                    <li class="flex items-center gap-2"><x-heroicon-o-check-circle class="h-5 w-5 text-marca" /> Sem estoque</li>
                    <li class="flex items-center gap-2"><x-heroicon-o-check-circle class="h-5 w-5 text-marca" /> Sem site</li>
                    <li class="flex items-center gap-2"><x-heroicon-o-check-circle class="h-5 w-5 text-marca" /> Só você, seu celular e os produtos Aquafast</li>
                </ul>

                <div class="mt-8 flex flex-wrap items-center gap-4">
                    @guest
                        <a href="{{ route('register') }}" class="botao-primario gap-2">
                            Quero ser afiliado Aquafast <x-heroicon-o-arrow-right class="h-4 w-4" />
                        </a>
                    @else
                        <a href="{{ route('app.painel') }}" class="botao-primario gap-2">
                            Ir para minha área <x-heroicon-o-arrow-right class="h-4 w-4" />
                        </a>
                    @endguest
                </div>
                <p class="mt-3 text-sm text-texto-3">É rápido, gratuito e sem burocracia.</p>
            </div>

            <div class="flex flex-col items-center lg:items-end">
                <p class="font-caveat mb-4 hidden -rotate-2 text-3xl font-bold text-marca sm:block sm:text-4xl" aria-hidden="true">
                    Você cria. A gente cresce junto.
                </p>

                {{-- Moldura de celular — o conteúdo do vídeo é ilustrativo, a ser substituído por gravações reais de afiliados --}}
                <div class="relative w-64 rounded-[2.5rem] border-[10px] border-texto bg-texto p-1.5 shadow-2xl sm:w-72">
                    <div class="relative aspect-[9/19.5] overflow-hidden rounded-[1.9rem] bg-gradient-to-b from-marca to-marca-suave">
                        <div class="absolute inset-x-0 top-0 flex items-center justify-between px-4 pt-3 text-xs font-semibold text-white/90">
                            <span>9:41</span>
                            <span>●●●</span>
                        </div>
                        <div class="absolute inset-x-0 top-9 flex justify-center gap-4 text-sm font-semibold text-white/70">
                            <span>Seguindo</span>
                            <span class="text-white">Para você</span>
                        </div>

                        <div class="absolute inset-0 flex flex-col items-center justify-center gap-2 text-white/80">
                            <span class="flex h-14 w-14 items-center justify-center rounded-full bg-white/15">
                                <x-heroicon-s-play class="h-6 w-6" />
                            </span>
                            <span class="text-xs">Exemplo de vídeo do afiliado</span>
                        </div>

                        <div class="absolute right-3 bottom-24 flex flex-col items-center gap-4 text-white">
                            <span class="flex flex-col items-center gap-1"><x-heroicon-s-heart class="h-6 w-6" /><span class="text-xs">25,4K</span></span>
                            <span class="flex flex-col items-center gap-1"><x-heroicon-o-chat-bubble-oval-left class="h-6 w-6" /><span class="text-xs">342</span></span>
                            <span class="flex flex-col items-center gap-1"><x-heroicon-o-arrow-uturn-right class="h-6 w-6" /><span class="text-xs">1,2K</span></span>
                        </div>

                        <div class="absolute inset-x-0 bottom-9 px-4 text-white">
                            <p class="text-sm font-semibold">Produtos que fazem a diferença no seu dia a dia 💙</p>
                            <p class="mt-1 text-xs text-white/70">@aquafast</p>
                        </div>

                        <div class="absolute inset-x-0 bottom-0 flex items-center justify-around bg-black/20 py-2 text-white/80" aria-hidden="true">
                            <x-heroicon-o-home class="h-5 w-5" />
                            <x-heroicon-o-magnifying-glass class="h-5 w-5" />
                            <span class="flex h-6 w-8 items-center justify-center rounded-md bg-white text-marca"><x-heroicon-s-plus class="h-4 w-4" /></span>
                            <x-heroicon-o-inbox class="h-5 w-5" />
                            <x-heroicon-o-user-circle class="h-5 w-5" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Como funciona --}}
    <section id="como-funciona" class="bg-superficie py-16 sm:py-24">
        <div class="mx-auto max-w-6xl px-5">
            <p class="text-sm font-semibold uppercase tracking-[.18em] text-marca">Em 4 passos simples</p>
            <h2 class="mt-3 text-3xl font-bold sm:text-4xl">Como funciona?</h2>
            <p class="mt-4 max-w-xl text-texto-2">Comece hoje mesmo e dê o primeiro passo para transformar suas redes em renda.</p>

            <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ([
                    ['icone' => 'user-plus', 'titulo' => 'Crie sua conta gratuitamente', 'texto' => 'Cadastre-se na Universidade de Afiliados Aquafast.'],
                    ['icone' => 'play', 'titulo' => 'Aprenda com nossos conteúdos', 'texto' => 'Acesse treinamentos exclusivos e veja como divulgar os produtos Aquafast da forma certa.'],
                    ['icone' => 'device-phone-mobile', 'titulo' => 'Poste seus vídeos', 'texto' => 'Use sua criatividade e compartilhe os produtos Aquafast no TikTok Shop.'],
                    ['icone' => 'chart-bar', 'titulo' => 'Ganhe comissão', 'texto' => 'Alguém comprou pelo seu conteúdo? Você recebe a comissão disponibilizada no TikTok Shop.'],
                ] as $indice => $passo)
                    <div class="relative rounded-2xl border border-linha bg-fundo p-6">
                        <div class="flex items-center gap-3">
                            <span class="flex h-9 w-9 items-center justify-center rounded-full bg-marca text-sm font-bold tabular-nums text-white">{{ $indice + 1 }}</span>
                            <x-dynamic-component :component="'heroicon-o-'.$passo['icone']" class="h-6 w-6 text-marca" />
                        </div>
                        <h3 class="mt-4 font-semibold text-texto">{{ $passo['titulo'] }}</h3>
                        <p class="mt-2 text-sm text-texto-2">{{ $passo['texto'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Produtos Aquafast --}}
    <section id="produtos" class="py-16 sm:py-24">
        <div class="mx-auto grid max-w-6xl gap-10 px-5 lg:grid-cols-[0.9fr_1.1fr] lg:items-center">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[.18em] text-marca">Produtos Aquafast</p>
                <h2 class="mt-3 text-3xl font-bold sm:text-4xl">Produtos que geram resultados.</h2>
                <p class="mt-4 text-texto-2">A Aquafast poderá liberar <strong class="text-texto">amostras reembolsáveis</strong>, mediante avaliação dos perfis que estejam alinhados à nossa marca.</p>
                <a href="{{ route('register') }}" class="botao-secundario mt-6 gap-2">
                    Conhecer os produtos <x-heroicon-o-arrow-right class="h-4 w-4" />
                </a>
            </div>

            <div class="grid grid-cols-2 gap-4">
                @foreach ([
                    ['bg' => 'bg-blue-50', 'texto' => 'text-blue-700', 'icone' => 'sparkles', 'rotulo' => 'Limpeza', 'desc' => 'Qualidade que o seu público confia.'],
                    ['bg' => 'bg-pink-50', 'texto' => 'text-pink-700', 'icone' => 'shield-check', 'rotulo' => 'Cuidado', 'desc' => 'Produtos para o dia a dia.'],
                    ['bg' => 'bg-green-50', 'texto' => 'text-green-700', 'icone' => 'cube', 'rotulo' => 'Praticidade', 'desc' => 'Soluções que fazem a diferença.'],
                    ['bg' => 'bg-amber-50', 'texto' => 'text-amber-700', 'icone' => 'star', 'rotulo' => 'Kits especiais', 'desc' => 'Mais valor para suas indicações.'],
                ] as $categoria)
                    <div class="rounded-2xl {{ $categoria['bg'] }} p-5">
                        <x-dynamic-component :component="'heroicon-o-'.$categoria['icone']" class="h-6 w-6 {{ $categoria['texto'] }}" />
                        <p class="mt-4 text-xs font-bold uppercase tracking-wide {{ $categoria['texto'] }}">{{ $categoria['rotulo'] }}</p>
                        <p class="mt-1 text-sm text-texto-2">{{ $categoria['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Por que fazer o curso --}}
    <section class="bg-marca py-16 text-white sm:py-24">
        <div class="mx-auto max-w-6xl px-5">
            <p class="text-sm font-semibold uppercase tracking-[.18em] text-white/70">Por que fazer o curso?</p>
            <h2 class="mt-3 max-w-2xl text-3xl font-bold sm:text-4xl">Mais do que um curso, é uma oportunidade.</h2>
            <p class="mt-4 max-w-xl text-white/80">Aqui você aprende, se conecta com uma marca forte e ainda pode transformar seu conteúdo em uma fonte de renda real.</p>

            <div class="mt-10 grid gap-8 sm:grid-cols-3">
                @foreach ([
                    ['icone' => 'academic-cap', 'titulo' => '100% gratuito', 'texto' => 'Sem taxas, sem surpresas.'],
                    ['icone' => 'user-group', 'titulo' => 'Suporte e comunidade', 'texto' => 'Tire dúvidas e troque experiências.'],
                    ['icone' => 'sparkles', 'titulo' => 'Crescimento junto com a marca', 'texto' => 'Quanto mais você cresce, mais a gente te valoriza.'],
                ] as $item)
                    <div>
                        <x-dynamic-component :component="'heroicon-o-'.$item['icone']" class="h-8 w-8 text-white" />
                        <p class="mt-4 font-semibold">{{ $item['titulo'] }}</p>
                        <p class="mt-1 text-sm text-white/70">{{ $item['texto'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Programa de Premiação --}}
    <section id="premiacao" class="py-16 sm:py-24">
        <div class="mx-auto max-w-6xl px-5">
            <div class="grid gap-10 lg:grid-cols-[0.7fr_1.3fr] lg:items-start">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[.18em] text-marca">Programa de Premiação</p>
                    <h2 class="mt-3 text-3xl font-bold sm:text-4xl">Você cria. Você vende. Você sobe de nível.</h2>
                    <p class="mt-4 text-texto-2">Quanto mais você cresce, mais a gente te recompensa. Além das comissões do TikTok Shop, reconhecemos os afiliados que se destacam divulgando a Aquafast.</p>
                    <a href="#premiacao-niveis" class="botao-secundario mt-6 gap-2">
                        Ver regras completas <x-heroicon-o-arrow-right class="h-4 w-4" />
                    </a>
                </div>

                <div id="premiacao-niveis">
                    <x-niveis-cards />
                    <p class="mt-4 text-xs text-texto-3">* Comissão conforme condições vigentes do programa/TikTok Shop.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Dúvidas --}}
    <section id="duvidas" class="bg-superficie py-16 sm:py-24">
        <div class="mx-auto max-w-3xl px-5">
            <p class="text-sm font-semibold uppercase tracking-[.18em] text-marca">Dúvidas</p>
            <h2 class="mt-3 text-3xl font-bold sm:text-4xl">Perguntas frequentes</h2>

            <div class="mt-10 space-y-5">
                @foreach ([
                    ['p' => 'Preciso ter estoque dos produtos?', 'r' => 'Não. Você divulga o kit disponível no TikTok Shop — a Aquafast cuida do estoque e da entrega.'],
                    ['p' => 'Preciso ter site ou loja própria?', 'r' => 'Não. Todo o processo de venda acontece dentro do TikTok Shop, a partir do seu conteúdo.'],
                    ['p' => 'O curso é realmente gratuito?', 'r' => 'Sim. Você não paga nada pelo curso e não precisa cadastrar cartão de crédito para acessá-lo.'],
                    ['p' => 'Como recebo produtos para gravar meus vídeos?', 'r' => 'A Aquafast poderá liberar amostras reembolsáveis, conforme avaliação do seu perfil e as regras vigentes da Aquafast e do TikTok Shop.'],
                ] as $duvida)
                    <div class="rounded-2xl border border-linha bg-fundo p-6">
                        <p class="font-semibold text-texto">{{ $duvida['p'] }}</p>
                        <p class="mt-2 text-sm text-texto-2">{{ $duvida['r'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- CTA final --}}
    <section class="bg-marca py-16 text-white sm:py-24">
        <div class="mx-auto flex max-w-6xl flex-col items-start gap-8 px-5 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[.18em] text-white/70">Pronto para começar?</p>
                <h2 class="mt-3 text-3xl font-bold sm:text-4xl">Seu conteúdo pode ir mais longe.</h2>
                <p class="mt-4 max-w-md text-white/80">Cadastre-se agora e faça parte da Universidade de Afiliados Aquafast.</p>
            </div>
            <div class="shrink-0">
                @guest
                    <a href="{{ route('register') }}" class="inline-flex items-center gap-2 rounded-md bg-white px-6 py-3 font-semibold text-marca transition hover:brightness-95">
                        Quero começar agora <x-heroicon-o-arrow-right class="h-4 w-4" />
                    </a>
                @else
                    <a href="{{ route('app.painel') }}" class="inline-flex items-center gap-2 rounded-md bg-white px-6 py-3 font-semibold text-marca transition hover:brightness-95">
                        Ir para minha área <x-heroicon-o-arrow-right class="h-4 w-4" />
                    </a>
                @endguest
                <p class="mt-3 text-sm text-white/70">É gratuito, rápido e sem complicação.</p>
            </div>
        </div>
    </section>
</div>
