<div>
    {{-- Hero --}}
    <section class="mx-auto max-w-6xl px-5 py-16 sm:py-24">
        <p class="text-sm font-semibold uppercase tracking-[.18em] text-marca">Curso 100% gratuito · TikTok Shop</p>
        <h1 class="mt-4 max-w-4xl text-balance text-4xl font-bold leading-tight sm:text-6xl">Aprenda. Crie. Venda. Ganhe.</h1>
        <p class="mt-6 max-w-2xl text-lg leading-8 text-texto-2">Aprenda a criar conteúdos para a Aquafast, divulgar nossos kits no TikTok Shop e ganhar comissão pelas suas vendas.</p>
        <p class="mt-3 max-w-2xl font-semibold text-texto">Sem estoque. Sem site. Só você, seu celular e os produtos Aquafast.</p>
        <div class="mt-8 flex flex-wrap gap-3">
            @guest
                <a href="{{ route('register') }}" class="botao-primario uppercase tracking-wide">Quero ser afiliado Aquafast</a>
            @else
                <a href="{{ route('app.painel') }}" class="botao-primario">Ir para minha área</a>
            @endguest
        </div>
    </section>

    {{-- Como você começa a ganhar dinheiro? --}}
    <section class="mx-auto max-w-6xl border-t border-linha px-5 py-16">
        <h2 class="text-2xl font-semibold sm:text-3xl">Como você começa a ganhar dinheiro?</h2>
        <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ([
                ['titulo' => 'Crie sua conta grátis', 'texto' => 'Cadastre-se gratuitamente e tenha acesso à Universidade de Afiliados Aquafast.'],
                ['titulo' => 'Aprenda a divulgar os produtos Aquafast', 'texto' => 'Acesse nossos conteúdos exclusivos e aprenda como usar, demonstrar e apresentar os produtos Aquafast no TikTok.'],
                ['titulo' => 'Poste vídeos com os produtos', 'texto' => 'Seguindo nosso passo a passo, você cria seus conteúdos e divulga os kits Aquafast disponíveis no TikTok Shop.'],
                ['titulo' => 'Receba comissão por cada venda', 'texto' => 'Alguém comprou pelo seu conteúdo? Você recebe a comissão disponibilizada no TikTok Shop.'],
            ] as $indice => $passo)
                <div class="rounded-lg border border-linha bg-superficie p-5">
                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-marca text-sm font-bold tabular-nums text-fundo">{{ $indice + 1 }}</span>
                    <h3 class="mt-4 font-semibold text-texto">{{ $passo['titulo'] }}</h3>
                    <p class="mt-2 text-sm text-texto-2">{{ $passo['texto'] }}</p>
                </div>
            @endforeach
        </div>
        <p class="mt-8 text-lg font-semibold text-texto">Você cria. Você indica. Você vende. Você ganha.</p>
    </section>

    {{-- E os produtos para começar? --}}
    <section class="mx-auto max-w-6xl border-t border-linha px-5 py-16">
        <h2 class="text-2xl font-semibold sm:text-3xl">E os produtos para começar?</h2>
        <p class="mt-4 max-w-3xl text-texto-2">A Aquafast poderá liberar <strong class="text-texto">amostras reembolsáveis</strong>, mediante avaliação dos perfis que estejam alinhados à marca.</p>
        <p class="mt-4 max-w-3xl text-texto-2">Assim, você pode conhecer os produtos, criar conteúdos reais e mostrar seus resultados para a sua audiência.</p>
        <p class="mt-4 max-w-3xl text-sm italic text-texto-3">A liberação está sujeita à análise do perfil e às regras vigentes da Aquafast e do TikTok Shop.</p>
    </section>

    {{-- Por que o curso é gratuito? --}}
    <section class="mx-auto max-w-6xl border-t border-linha px-5 py-16">
        <h2 class="text-2xl font-semibold sm:text-3xl">Por que o curso é gratuito?</h2>
        <p class="mt-4 max-w-3xl text-texto-2">Porque quando nossos afiliados crescem, a Aquafast cresce junto.</p>
        <p class="mt-4 max-w-3xl text-texto-2">Quanto mais você conhecer os produtos e souber demonstrar seus benefícios, maiores são as possibilidades de criar bons conteúdos e gerar vendas. Por isso, todo o treinamento da Universidade de Afiliados Aquafast é gratuito.</p>
        <p class="mt-4 max-w-3xl font-semibold text-texto">Você não paga nada pelo curso e não precisa cadastrar cartão de crédito para acessá-lo.</p>
    </section>

    {{-- Programa de Premiação --}}
    <section class="mx-auto max-w-6xl border-t border-linha px-5 py-16">
        <h2 class="text-2xl font-semibold sm:text-3xl">Programa de Premiação</h2>
        <p class="mt-4 max-w-3xl text-texto-2">Quanto mais você cresce, mais a gente te recompensa. Além das comissões do TikTok Shop, queremos reconhecer os afiliados que se destacam divulgando e vendendo Aquafast.</p>
        <div class="mt-8">
            <x-tabela-niveis />
        </div>
        <p class="mt-6 text-lg font-semibold text-texto">Você cria. Você vende. Você sobe de nível. E a Aquafast recompensa sua evolução.</p>
        <p class="mt-3 text-sm text-texto-3">* Comissão conforme condições vigentes do programa/TikTok Shop.</p>
    </section>

    {{-- CTA final --}}
    <section class="mx-auto max-w-6xl border-t border-linha px-5 py-16 text-center sm:py-24">
        <h2 class="text-3xl font-semibold sm:text-4xl">Pronto para começar?</h2>
        <p class="mx-auto mt-4 max-w-xl text-texto-2">Transforme seus conteúdos em uma nova oportunidade de renda. Cadastre-se gratuitamente na Universidade de Afiliados Aquafast.</p>
        <div class="mt-8 flex justify-center">
            @guest
                <a href="{{ route('register') }}" class="botao-primario uppercase tracking-wide">Quero começar</a>
            @else
                <a href="{{ route('app.painel') }}" class="botao-primario">Ir para minha área</a>
            @endguest
        </div>
    </section>
</div>
