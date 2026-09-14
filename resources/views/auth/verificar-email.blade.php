<x-layouts.publico titulo="Verifique seu e-mail">
    <section class="mx-auto max-w-2xl px-5 py-20 text-center">
        <h1 class="text-3xl font-semibold">Confirme seu e-mail</h1>
        <p class="mt-4 text-texto-2">Enviamos um link de confirmação para seu endereço. Abra o link antes de acessar os treinamentos.</p>
        @if (session('status') === 'verification-link-sent')<div class="sucesso mt-6" role="status">Um novo link foi enviado.</div>@endif
        <form class="mt-8" method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button class="botao-secundario" type="submit">Reenviar e-mail</button>
        </form>
    </section>
</x-layouts.publico>
