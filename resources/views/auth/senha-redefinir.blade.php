<x-layouts.publico titulo="Redefinir senha">
    <section class="mx-auto max-w-md px-5 py-14">
        <h1 class="text-3xl font-semibold">Redefinir senha</h1>
        <form class="mt-8 space-y-5" method="POST" action="{{ route('password.update') }}">
            @csrf
            <input name="token" type="hidden" value="{{ $request->route('token') }}">
            <label class="campo">E-mail<input name="email" type="email" value="{{ old('email', $request->email) }}" required></label>
            <label class="campo">Nova senha<input name="password" type="password" autocomplete="new-password" required></label>
            <label class="campo">Confirmar senha<input name="password_confirmation" type="password" autocomplete="new-password" required></label>
            @if ($errors->any())<div class="erro" role="alert">{{ $errors->first() }}</div>@endif
            <button class="botao-primario w-full" type="submit">Salvar nova senha</button>
        </form>
    </section>
</x-layouts.publico>
