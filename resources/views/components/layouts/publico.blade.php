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
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-fundo text-texto antialiased">
    <header class="sticky top-0 z-20 border-b border-linha bg-fundo/80 backdrop-blur">
        <nav class="mx-auto flex max-w-6xl items-center justify-between px-5 py-4" aria-label="Navegação principal">
            <a href="{{ route('vitrine') }}" class="flex items-center gap-2 text-lg font-semibold tracking-tight text-texto focus-ring">
                <img src="{{ asset('images/marca/aquafast-logo-navy.svg') }}" alt="Aquafast" class="h-7 w-auto">
                <span>Universidade</span>
            </a>
            <div class="hidden items-center gap-6 text-sm font-medium text-texto-2 lg:flex">
                <a class="link" href="{{ route('vitrine') }}#como-funciona">Como funciona</a>
                <a class="link" href="{{ route('vitrine') }}#produtos">Produtos</a>
                <a class="link" href="{{ route('vitrine') }}#premiacao">Premiação</a>
                <a class="link" href="{{ route('vitrine') }}#duvidas">Dúvidas</a>
            </div>
            <div class="flex items-center gap-3 text-sm">
                @auth
                    <a href="{{ route('app.painel') }}" class="botao-primario">Minha área</a>
                @else
                    <a href="{{ route('login') }}" class="link hidden sm:inline">Entrar</a>
                    <a href="{{ route('register') }}" class="botao-primario">Criar conta grátis</a>
                @endauth
            </div>
        </nav>
    </header>
    <main>{{ $slot }}</main>
    <footer class="border-t border-linha bg-fundo">
        <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-6 px-5 py-10">
            <a href="{{ route('vitrine') }}" class="flex items-center gap-2 font-semibold text-texto focus-ring">
                <img src="{{ asset('images/marca/aquafast-logo-navy.svg') }}" alt="Aquafast" class="h-6 w-auto">
                <span>Universidade</span>
            </a>
            <div class="flex flex-wrap gap-5 text-sm font-medium text-texto-2">
                <a class="link" href="{{ route('vitrine') }}#como-funciona">Como funciona</a>
                <a class="link" href="{{ route('vitrine') }}#produtos">Produtos</a>
                <a class="link" href="{{ route('vitrine') }}#premiacao">Premiação</a>
            </div>
            <div class="flex gap-4 text-sm font-medium text-texto-2">
                <span>TikTok</span>
                <span>Instagram</span>
                <span>YouTube</span>
            </div>
            <p class="w-full text-sm text-texto-3 lg:w-auto">Juntos por um futuro mais limpo.</p>
        </div>
        <div class="border-t border-linha">
            <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-4 px-5 py-6 text-xs text-texto-3">
                <div class="flex flex-wrap gap-5">
                    <span>© {{ date('Y') }} Universidade Aquafast</span>
                    <a class="link" href="{{ route('termos') }}">Termos de uso</a>
                    <a class="link" href="{{ route('privacidade') }}">Privacidade</a>
                </div>
                <span>Desenvolvido pela TI Aquafast</span>
            </div>
        </div>
    </footer>
</body>
</html>
