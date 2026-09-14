<x-layouts.aluno titulo="Minha área">
    <div class="rounded-lg border border-linha bg-superficie p-7">
        <p class="text-sm text-marca">Olá, {{ auth()->user()->nome }}</p>
        <h1 class="mt-2 text-3xl font-semibold">Sua área de treinamento está pronta.</h1>
        <p class="mt-4 max-w-2xl text-texto-2">Os cursos e o catálogo serão adicionados na próxima etapa do projeto.</p>
    </div>
</x-layouts.aluno>
