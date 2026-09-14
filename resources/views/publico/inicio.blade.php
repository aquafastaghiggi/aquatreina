<x-layouts.publico titulo="Aquafast Treina">
    <section class="mx-auto grid max-w-6xl gap-10 px-5 py-20 lg:grid-cols-[1.2fr_.8fr] lg:items-center">
        <div>
            <p class="mb-4 text-sm font-semibold uppercase tracking-[.18em] text-marca">Treinamento técnico Aquafast</p>
            <h1 class="max-w-3xl text-balance text-4xl font-bold leading-tight sm:text-6xl">Conhecimento de produto para usar no trabalho.</h1>
            <p class="mt-6 max-w-2xl text-lg leading-8 text-texto-2">Aprenda no seu ritmo, reveja quando precisar e mantenha os materiais técnicos sempre por perto.</p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ route('register') }}" class="botao-primario">Criar minha conta</a>
                <a href="{{ route('login') }}" class="botao-secundario">Já tenho conta</a>
            </div>
        </div>
        <div class="rounded-lg border border-linha bg-superficie p-7">
            <p class="text-sm text-texto-3">A fundação está pronta</p>
            <h2 class="mt-2 text-2xl font-semibold">Ambiente de treinamento seguro</h2>
            <p class="mt-3 leading-7 text-texto-2">Cadastro verificado por e-mail e acesso sujeito à aprovação da Aquafast.</p>
        </div>
    </section>
</x-layouts.publico>
