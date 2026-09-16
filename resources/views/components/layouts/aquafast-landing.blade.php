<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="theme-color" content="#0b2f87" />
  <title>{{ $titulo ?? 'Universidade Aquafast' }}</title>
  @isset($descricao)<meta name="description" content="{{ $descricao }}">@endisset
  @isset($ogTitulo)<meta property="og:title" content="{{ $ogTitulo }}">@endisset
  @isset($ogDescricao)<meta property="og:description" content="{{ $ogDescricao }}">@endisset
  @isset($ogUrl)<meta property="og:type" content="website"><meta property="og:url" content="{{ $ogUrl }}">@endisset
  @if (! empty($ogImagem))<meta property="og:image" content="{{ $ogImagem }}">@endif
  <link rel="icon" type="image/png" href="{{ asset('images/marca/aquafast-favicon-300.png') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/aquafast-landing.css') }}" />
</head>
<body>
  <header class="topbar">
    <div class="container nav-wrap">
      <a class="brand" href="{{ route('vitrine') }}#inicio" aria-label="Aquafast Universidade">
        <span class="brand-aqua">AQUA<span>FAST</span></span>
        <span class="brand-divider"></span>
        <span class="brand-university">Universidade</span>
      </a>

      <nav class="nav-links" aria-label="Navegação principal">
        <a href="{{ route('vitrine') }}#como-funciona">Como funciona</a>
        <a href="{{ route('vitrine') }}#produtos">Produtos</a>
        <a href="{{ route('vitrine') }}#premiacao">Premiação</a>
      </nav>

      <div class="nav-actions">
        @auth
          <a class="btn btn-primary btn-small" href="{{ route('app.painel') }}">Minha área</a>
        @else
          <a class="login" href="{{ route('login') }}">Entrar</a>
          <a class="btn btn-primary btn-small" href="{{ route('register') }}">Criar conta grátis</a>
        @endauth
      </div>
    </div>
  </header>

  <main>{{ $slot }}</main>

  <footer class="footer">
    <div class="container footer-grid">
      <a class="brand" href="{{ route('vitrine') }}#inicio"><span class="brand-aqua">AQUA<span>FAST</span></span><span class="brand-divider"></span><span class="brand-university">Universidade</span></a>
      <nav><a href="{{ route('vitrine') }}#como-funciona">Como funciona</a><a href="{{ route('vitrine') }}#produtos">Produtos</a><a href="{{ route('vitrine') }}#premiacao">Premiação</a></nav>
      <div class="social">♪　◎　▶</div>
      <p>Juntos por um<br>futuro mais limpo.</p>
    </div>
    <div class="container" style="padding-top:18px;font-size:11px;color:#8a95a8;display:flex;flex-wrap:wrap;gap:18px;justify-content:space-between">
      <span>© {{ date('Y') }} Universidade Aquafast · <a href="{{ route('termos') }}" style="text-decoration:underline">Termos de uso</a> · <a href="{{ route('privacidade') }}" style="text-decoration:underline">Privacidade</a></span>
      <span>Desenvolvido pela TI Aquafast</span>
    </div>
  </footer>

  <script src="{{ asset('js/aquafast-landing.js') }}"></script>
</body>
</html>
