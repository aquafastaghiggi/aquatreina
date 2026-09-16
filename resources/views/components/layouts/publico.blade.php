<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $titulo ?? 'Universidade Aquafast' }}</title>
    @isset($descricao)<meta name="description" content="{{ $descricao }}">@endisset
    @isset($ogTitulo)<meta property="og:title" content="{{ $ogTitulo }}">@endisset
    @isset($ogDescricao)<meta property="og:description" content="{{ $ogDescricao }}">@endisset
    @isset($ogUrl)<meta property="og:type" content="website"><meta property="og:url" content="{{ $ogUrl }}">@endisset
    @if (! empty($ogImagem))<meta property="og:image" content="{{ $ogImagem }}">@endif
    <link rel="icon" type="image/png" href="{{ asset('images/marca/aquafast-favicon-300.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-fundo text-texto antialiased">
    <header class="border-b border-linha bg-superficie/80 backdrop-blur">
        <nav class="mx-auto flex max-w-6xl items-center justify-between px-5 py-4" aria-label="Navegação principal">
            <a href="{{ route('vitrine') }}" class="flex items-center gap-2 text-lg font-semibold tracking-tight text-texto focus-ring">
                <img src="{{ asset('images/marca/aquafast-logo-navy.svg') }}" alt="Aquafast" class="h-7 w-auto">
                <span>Universidade</span>
            </a>
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
    <footer class="mx-auto mt-16 flex max-w-6xl flex-wrap items-center justify-between gap-5 border-t border-linha px-5 py-8 text-sm text-texto-3">
        <div class="flex flex-wrap gap-5">
            <span>© {{ date('Y') }} Universidade Aquafast</span>
            <a class="link" href="{{ route('termos') }}">Termos de uso</a>
            <a class="link" href="{{ route('privacidade') }}">Privacidade</a>
        </div>
        <span>Desenvolvido pela TI Aquafast</span>
    </footer>
</body>
</html>
