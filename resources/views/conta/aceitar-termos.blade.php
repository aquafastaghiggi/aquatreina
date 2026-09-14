<x-layouts.publico titulo="Atualização dos termos">
    <main class="mx-auto max-w-2xl px-5 py-14"><h1 class="text-3xl font-semibold">Termos atualizados</h1>
        <div class="prosa mt-6 whitespace-pre-line">{{ $texto?->conteudo }}</div>
        <form method="POST" action="{{ route('termos.aceitar') }}" class="mt-8">@csrf
            <label class="flex gap-3"><input type="checkbox" name="aceite_termos" value="1" required> Li e aceito a versão {{ $texto?->versao }} dos termos.</label>
            <button class="botao-primario mt-5" type="submit">Continuar</button>
        </form>
    </main>
</x-layouts.publico>
