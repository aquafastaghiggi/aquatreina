<x-layouts.aluno :titulo="$curso->titulo">
    @if (session('sucesso'))<div class="sucesso mb-6">{{ session('sucesso') }}</div>@endif
    <p class="text-sm font-semibold text-marca">Treinamento</p>
    <h1 class="mt-2 text-3xl font-semibold">{{ $curso->titulo }}</h1>
    <div class="mt-7 rounded-lg border border-linha bg-superficie p-7">
        <p class="text-texto-2">Sua matrícula está ativa. A sala de aula será habilitada na próxima etapa.</p>
        <a href="{{ route('app.painel') }}" class="botao-secundario mt-5">Voltar para minha área</a>
    </div>
</x-layouts.aluno>
