<x-layouts.publico titulo="Criar conta">
    <section class="mx-auto max-w-xl px-5 py-14">
        <h1 class="text-3xl font-semibold">Criar conta</h1>
        <p class="mt-2 text-texto-2">Preencha seus dados profissionais.</p>
        <form class="mt-8 grid gap-5 sm:grid-cols-2" method="POST" action="{{ route('register.store') }}">
            @csrf
            <label class="campo sm:col-span-2">Nome<input name="nome" value="{{ old('nome') }}" autocomplete="name" required autofocus></label>
            <label class="campo sm:col-span-2">E-mail<input name="email" type="email" value="{{ old('email') }}" autocomplete="email" required></label>
            <label class="campo">Telefone<input name="telefone" value="{{ old('telefone') }}" autocomplete="tel"></label>
            <label class="campo">Cargo<input name="cargo" value="{{ old('cargo') }}" autocomplete="organization-title"></label>
            <label class="campo sm:col-span-2">Empresa<input name="empresa" value="{{ old('empresa') }}" autocomplete="organization"></label>
            <label class="campo">Senha<input name="password" type="password" autocomplete="new-password" required></label>
            <label class="campo">Confirmar senha<input name="password_confirmation" type="password" autocomplete="new-password" required></label>
            <div class="absolute -left-[9999px]" aria-hidden="true"><label>Website<input name="website" tabindex="-1" autocomplete="off"></label></div>
            <label class="flex items-start gap-3 text-sm text-texto-2 sm:col-span-2"><input class="mt-1" name="aceite_termos" type="checkbox" value="1" required> <span>Li e aceito os <a class="link" href="{{ route('termos') }}" target="_blank">termos de uso</a> e a <a class="link" href="{{ route('privacidade') }}" target="_blank">política de privacidade</a>.</span></label>
            @if ($errors->any())<div class="erro sm:col-span-2" role="alert">{{ $errors->first() }}</div>@endif
            <button class="botao-primario sm:col-span-2" type="submit">Criar conta</button>
        </form>
    </section>
</x-layouts.publico>
