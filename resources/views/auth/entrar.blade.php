<x-layouts.publico titulo="Entrar">
    <section class="mx-auto max-w-md px-5 py-14">
        <h1 class="text-3xl font-semibold">Entrar</h1>
        <p class="mt-2 text-texto-2">Acesse seus treinamentos Aquafast.</p>
        <form class="mt-8 space-y-5" method="POST" action="{{ route('login.store') }}">
            @csrf
            <label class="campo">E-mail<input name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus></label>
            <label class="campo">Senha<input name="password" type="password" autocomplete="current-password" required></label>
            <label class="flex items-center gap-2 text-sm text-texto-2"><input name="remember" type="checkbox"> Lembrar de mim</label>
            @if ($errors->any())<div class="erro" role="alert">{{ $errors->first() }}</div>@endif
            <button class="botao-primario w-full" type="submit">Entrar</button>
            <a class="link block text-center text-sm" href="{{ route('password.request') }}">Esqueci minha senha</a>
        </form>
    </section>
</x-layouts.publico>
