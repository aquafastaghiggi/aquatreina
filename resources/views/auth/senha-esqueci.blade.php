<x-layouts.publico titulo="Recuperar senha">
    <section class="mx-auto max-w-md px-5 py-14">
        <h1 class="text-3xl font-semibold">Recuperar senha</h1>
        <p class="mt-2 text-texto-2">Enviaremos um link para você definir uma nova senha.</p>
        <form class="mt-8 space-y-5" method="POST" action="{{ route('password.email') }}">
            @csrf
            <label class="campo">E-mail<input name="email" type="email" value="{{ old('email') }}" required autofocus></label>
            @if (session('status'))<div class="sucesso" role="status">{{ session('status') }}</div>@endif
            @if ($errors->any())<div class="erro" role="alert">{{ $errors->first() }}</div>@endif
            <button class="botao-primario w-full" type="submit">Enviar link</button>
        </form>
    </section>
</x-layouts.publico>
