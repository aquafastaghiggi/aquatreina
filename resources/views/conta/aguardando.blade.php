<x-layouts.publico titulo="Aguardando aprovação">
    <section class="mx-auto max-w-2xl px-5 py-20 text-center">
        <div class="mx-auto mb-5 grid size-14 place-items-center rounded-full bg-marca-suave text-2xl text-marca" aria-hidden="true">✓</div>
        <h1 class="text-3xl font-semibold">E-mail confirmado. Agora falta a aprovação.</h1>
        <p class="mt-4 leading-7 text-texto-2">A equipe Aquafast vai revisar seu cadastro. Assim que sua conta for aprovada, sua área de treinamento será liberada.</p>
        <form class="mt-8" method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="botao-secundario" type="submit">Sair</button>
        </form>
    </section>
</x-layouts.publico>
