<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $titulo ?? 'Minha área' }} — Aquafast Treina</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-fundo text-texto antialiased">
    <header class="border-b border-linha bg-superficie">
        <nav class="mx-auto flex max-w-6xl items-center justify-between px-5 py-4" aria-label="Área do aluno">
            <a href="{{ route('app.painel') }}" class="font-semibold focus-ring">Aquafast <span class="text-marca">Treina</span></a>
            <div class="flex items-center gap-4 text-sm">
                <span class="hidden text-texto-2 sm:inline">{{ auth()->user()->nome }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="link" type="submit">Sair</button>
                </form>
            </div>
        </nav>
    </header>
    <main class="mx-auto max-w-6xl px-5 py-10">{{ $slot }}</main>
</body>
</html>
