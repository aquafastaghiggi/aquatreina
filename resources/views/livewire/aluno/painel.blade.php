<div class="space-y-16">
    @if (session('sucesso'))<div class="sucesso">{{ session('sucesso') }}</div>@endif

    <div class="flex flex-wrap items-end justify-between gap-4">
        <div><p class="text-sm text-marca">Olá, {{ auth()->user()->nome }}</p><h1 class="mt-1 text-3xl font-semibold">Sua Universidade Aquafast</h1></div>
        <div class="flex gap-4"><a href="{{ route('app.perfil') }}" class="link">Perfil</a></div>
    </div>

    @if ($textoBoasVindas !== '')
        <div class="rounded-lg border border-marca/40 bg-marca-suave p-4 text-texto-2">{{ $textoBoasVindas }}</div>
    @endif

    {{-- Bem-vindo à Aquafast --}}
    <section>
        <h2 class="text-2xl font-semibold">Bem-vindo à Aquafast</h2>
        <p class="mt-4 max-w-3xl text-texto-2">Antes de criar seu primeiro conteúdo, queremos que você conheça um pouco mais sobre quem somos.</p>
        <p class="mt-4 max-w-3xl text-texto-2">A Aquafast nasceu em 2001, em Guaporé, no Rio Grande do Sul, e construiu sua história levando produtos de limpeza, cuidado e perfumação para milhares de lares, tornando-se <strong class="text-texto">uma das principais marcas do segmento no Sul do país</strong>.</p>
        <p class="mt-4 max-w-3xl text-texto-2">Aqui você vai conhecer nossa história, nossa estrutura e tudo o que existe por trás dos produtos que agora você também poderá apresentar para a sua audiência.</p>
        <div class="mt-6 grid gap-4 sm:grid-cols-2">
            <img src="{{ asset('images/landing/empresa.webp') }}" alt="Sede da Aquafast" class="aspect-video w-full rounded-lg object-cover">
            <video src="{{ asset('videos/institucional.mp4') }}" controls preload="metadata" class="aspect-video w-full rounded-lg bg-black"></video>
        </div>
    </section>

    {{-- Mais que afiliados --}}
    <section class="border-t border-linha pt-12">
        <h2 class="text-2xl font-semibold">Mais do que afiliados, queremos educadores da nossa marca.</h2>
        <p class="mt-4 max-w-3xl text-texto-2">Para a Aquafast, um bom conteúdo não deve apenas mostrar um produto. <strong class="text-texto">Ele precisa ensinar.</strong></p>
        <p class="mt-4 max-w-3xl text-texto-2">Quem assiste ao seu vídeo precisa entender qual problema aquele produto ajuda a resolver, como utilizá-lo corretamente e qual resultado pode esperar.</p>
        <p class="mt-4 max-w-3xl text-texto-2">Por isso, vamos ensinar você a conhecer nossos produtos e transformá-los em conteúdos simples, reais e que ajudem o consumidor.</p>
    </section>

    {{-- Fórmula de conteúdo --}}
    <section class="border-t border-linha pt-12">
        <h2 class="text-2xl font-semibold">A fórmula de um bom conteúdo Aquafast</h2>
        <div class="mt-6 grid gap-5 sm:grid-cols-2">
            <div><h3 class="font-semibold text-marca">1. Mostre a dor</h3><p class="mt-2 text-sm text-texto-2">Comece por uma situação que o consumidor reconheça no seu dia a dia — uma mancha difícil, um tênis sujo, uma roupa que precisa ficar mais perfumada, uma limpeza pesada, falta de tempo ou outro problema do dia a dia que faça sentido para aquele produto.</p></div>
            <div><h3 class="font-semibold text-marca">2. Apresente a solução</h3><p class="mt-2 text-sm text-texto-2">Apresente o produto Aquafast indicado para aquela situação e mostre seus principais diferenciais.</p></div>
            <div><h3 class="font-semibold text-marca">3. Ensine como usar</h3><p class="mt-2 text-sm text-texto-2">Mostre o produto sendo utilizado na prática, sempre seguindo as orientações corretas de uso.</p></div>
            <div><h3 class="font-semibold text-marca">4. Mostre o resultado</h3><p class="mt-2 text-sm text-texto-2">Sempre mostre o antes e depois. Deixe o consumidor enxergar a transformação.</p></div>
            <div class="sm:col-span-2"><h3 class="font-semibold text-marca">5. Mostre onde comprar</h3><p class="mt-2 text-sm text-texto-2">Finalize direcionando para o kit disponível no TikTok Shop. Mostre a sacolinha e incentive o consumidor a clicar para conhecer o produto.</p></div>
        </div>
        <div class="mt-8">
            <x-formula-conteudo />
        </div>
    </section>

    {{-- Trilhas de produto --}}
    <section class="border-t border-linha pt-12">
        <h2 class="text-2xl font-semibold">Agora vamos conhecer os produtos?</h2>
        <p class="mt-4 max-w-3xl text-texto-2">Preparamos uma trilha de conteúdos para ensinar você a apresentar e demonstrar corretamente cada produto Aquafast. Escolha o produto que deseja conhecer.</p>

        @if ($this->produtos->isEmpty())
            <div class="mt-8 rounded-lg border border-linha bg-superficie p-8 text-center">
                <h3 class="text-xl font-semibold">Os primeiros produtos estão sendo preparados.</h3>
                <p class="mt-3 text-texto-2">Em breve você vai poder escolher um produto e começar a criar conteúdo.</p>
            </div>
        @else
            <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($this->produtos as $produto)
                    <x-curso-card
                        wire:key="produto-{{ $produto->id }}-{{ $loop->index }}"
                        :curso="$produto"
                        :capa-url="$apresentacao->urlCapa($produto)"
                        :inscrito="$produto->minha_matricula !== null"
                        :percentual="$produto->minha_matricula?->percentual_progresso"
                        :videos-embed="$produto->videos_embed"
                    />
                @endforeach
            </div>
        @endif
    </section>

    {{-- Boas práticas --}}
    <section class="border-t border-linha pt-12">
        <h2 class="text-2xl font-semibold">Boas práticas para seus conteúdos</h2>
        <p class="mt-4 max-w-3xl text-texto-2">Queremos conteúdos naturais, criativos e verdadeiros. Mostre situações reais do seu dia a dia, demonstre os produtos e explique de maneira simples como eles podem ajudar o consumidor.</p>
        <p class="mt-4 max-w-3xl text-texto-2">Não é necessário produzir vídeos extremamente elaborados. <strong class="text-texto">O mais importante é mostrar, ensinar e demonstrar.</strong></p>
        <div class="mt-6">
            <x-formula-conteudo :compacta="true" />
        </div>
    </section>

    {{-- Cresça com a Aquafast --}}
    <section class="border-t border-linha pt-12">
        <h2 class="text-2xl font-semibold">Cresça com a Aquafast</h2>
        <p class="mt-4 max-w-3xl text-texto-2">Seu primeiro vídeo pode ser apenas o começo. Os afiliados que se destacarem poderão avançar no programa, conquistar premiações, receber produtos, ter acesso antecipado a lançamentos e entrar no radar da Aquafast para futuras campanhas e parcerias.</p>
        <div class="mt-8">
            <x-tabela-niveis />
        </div>
        <p class="mt-8 text-center text-xl font-semibold uppercase tracking-wide text-texto">Crie. Ensine. Venda. Cresça com a Aquafast.</p>
    </section>

</div>
