<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $titulo ?? 'Aquafast Treina' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-fundo text-texto antialiased">
    <header class="border-b border-linha bg-superficie/80 backdrop-blur">
        <nav class="mx-auto flex max-w-6xl items-center justify-between px-5 py-4" aria-label="Navegação principal">
            <a href="{{ route('vitrine') }}" class="text-lg font-semibold tracking-tight text-texto focus-ring">Aquafast <span class="text-marca">Treina</span></a>
            <div class="flex items-center gap-3 text-sm">
                @auth
                    <a href="{{ route('app.painel') }}" class="botao-primario">Minha área</a>
                @else
                    <a href="{{ route('login') }}" class="link">Entrar</a>
                    <a href="{{ route('register') }}" class="botao-primario">Criar conta</a>
                @endauth
            </div>
        </nav>
    </header>
    <main>{{ $slot }}</main>
    <footer class="mx-auto mt-16 flex max-w-6xl flex-wrap gap-5 border-t border-linha px-5 py-8 text-sm text-texto-3">
        <span>© {{ date('Y') }} Aquafast</span>
        <a class="link" href="{{ route('termos') }}">Termos de uso</a>
        <a class="link" href="{{ route('privacidade') }}">Privacidade</a>
    </footer>
</body>
</html>
