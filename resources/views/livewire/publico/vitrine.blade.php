<div>
<section class="hero" id="inicio">
  <div class="container hero-grid">
    <div class="hero-copy">
      <span class="eyebrow">CURSO 100% GRATUITO · TIKTOK SHOP</span>
      <h1>Transforme<br>seu conteúdo<br><span>em renda.</span></h1>
      <p class="hero-text">Aprenda a criar conteúdos para a Aquafast, divulgar nossos kits no TikTok Shop e ganhar comissão por cada venda.</p>

      <div class="hero-benefits">
        <span>✓ Sem estoque</span>
        <span>✓ Sem site</span>
        <span>✓ Só você, seu celular<br>e os produtos Aquafast</span>
      </div>

      @guest
        <a class="btn btn-primary btn-large" href="{{ route('register') }}">Quero ser afiliado Aquafast <span>→</span></a>
      @else
        <a class="btn btn-primary btn-large" href="{{ route('app.painel') }}">Ir para minha área <span>→</span></a>
      @endguest
      <p class="microcopy">É rápido, gratuito e sem burocracia.</p>
    </div>

    <div class="hero-visual" aria-label="Visual de divulgação Aquafast">
      <div class="blur-orb orb-1"></div>
      <div class="blur-orb orb-2"></div>

      <div class="phone">
        <div class="phone-notch"></div>
        <div class="phone-screen">
          <div class="phone-top">8:01 <span>◉ ◉ ◉</span></div>
          <div class="phone-person">
            <div class="person-head"></div>
            <div class="person-body"></div>
            <div class="bottle bottle-main">AQUA<br>FAST</div>
          </div>
          <div class="phone-caption">Produtos que<br>fazem a diferença<br>no seu dia a dia 💙</div>
          <div class="phone-user">@aquafast</div>
          <div class="phone-bottom">⌂　⌕　＋　▣　◉</div>
        </div>
      </div>

      <div class="handwritten">Você<br>cria.<br>A gente<br>cresce<br>junto.</div>

      <div class="hero-products">
        <div class="prod-shape tall"></div>
        <div class="prod-shape white"></div>
        <div class="prod-shape pouch"></div>
      </div>
    </div>
  </div>
</section>

<section class="steps-section" id="como-funciona">
  <div class="container steps-layout">
    <div class="steps-intro">
      <span class="eyebrow eyebrow-plain">EM 4 PASSOS SIMPLES</span>
      <h2>Como funciona?</h2>
      <p>Comece hoje mesmo e dê o primeiro passo para transformar suas redes em renda.</p>
    </div>

    <div class="steps-grid">
      <article class="step-card"><span class="step-num">1</span><div class="step-icon">♙+</div><h3>Crie sua conta gratuitamente</h3><p>Cadastre-se na Universidade de Afiliados Aquafast.</p></article>
      <div class="step-arrow">→</div>
      <article class="step-card"><span class="step-num">2</span><div class="step-icon">▶</div><h3>Aprenda com nossos conteúdos</h3><p>Acesse treinamentos exclusivos e veja como divulgar os produtos da forma certa.</p></article>
      <div class="step-arrow">→</div>
      <article class="step-card"><span class="step-num">3</span><div class="step-icon">▯</div><h3>Poste seus vídeos</h3><p>Use sua criatividade e compartilhe os produtos Aquafast no TikTok Shop.</p></article>
      <div class="step-arrow">→</div>
      <article class="step-card"><span class="step-num">4</span><div class="step-icon">▮▮▮</div><h3>Ganhe comissão</h3><p>Alguém comprou pelo seu conteúdo? Você recebe a comissão disponibilizada no TikTok Shop.</p></article>
    </div>
  </div>
</section>

<section class="products-section" id="produtos">
  <div class="container products-layout">
    <div class="products-copy">
      <span class="eyebrow eyebrow-plain">PRODUTOS AQUAFAST</span>
      <h2>Produtos que<br>geram resultados.</h2>
      <p>A Aquafast poderá liberar amostras reembolsáveis, mediante avaliação dos perfis que estejam alinhados à nossa marca.</p>
      @guest
        <a class="btn btn-outline" href="{{ route('register') }}">Conheça os produtos <span>→</span></a>
      @else
        <a class="btn btn-outline" href="{{ route('app.painel') }}">Conheça os produtos <span>→</span></a>
      @endguest
    </div>

    <div class="product-cards">
      <article class="product-card blue"><div class="product-visual bottle-blue"></div><span class="product-tag">⚙ LIMPEZA</span><p>Qualidade que<br>o seu público confia.</p></article>
      <article class="product-card pink"><div class="product-visual bottle-white"></div><span class="product-tag">♙ CUIDADO</span><p>Produtos para o<br>dia a dia.</p></article>
      <article class="product-card green"><div class="product-visual wipes"></div><span class="product-tag">♲ PRATICIDADE</span><p>Soluções que fazem<br>a diferença.</p></article>
      <article class="product-card yellow"><div class="product-visual kit"></div><span class="product-tag">☆ KITS ESPECIAIS</span><p>Mais valor para<br>suas indicações.</p></article>
    </div>
  </div>
</section>

<section class="rewards-section" id="premiacao">
  <div class="container rewards-layout">
    <div class="rewards-copy">
      <span class="eyebrow eyebrow-plain">PROGRAMA DE PREMIAÇÃO</span>
      <h2>Você cria. Você vende.<br>Você sobe de nível.</h2>
      <p>Quanto mais você cresce, mais a gente te recompensa. Além das comissões do TikTok Shop, reconhecemos os afiliados que se destacam divulgando a Aquafast.</p>
      <a class="btn btn-outline" href="#premiacao">Ver regras completas <span>→</span></a>
    </div>

    <div class="reward-cards">
      @foreach (config('premiacao.niveis') as $indice => $nivel)
        <article class="reward-card level{{ $indice + 1 }}">
          <strong>{{ $indice === 5 ? '♛' : '★' }}</strong>
          <h3>Nível {{ $indice + 1 }}<br>{{ $nivel['nome'] }}</h3>
          <b>{{ $nivel['faturamento'] }}</b>
          <p>{{ $nivel['reconhecimento'] }}</p>
        </article>
      @endforeach
    </div>
  </div>
  <p class="container footnote">* Comissão conforme condições vigentes do programa/TikTok Shop.</p>
</section>

<section class="final-cta" id="cta-final">
  <div class="container cta-box">
    <div>
      <span class="eyebrow eyebrow-light">PRONTO PARA COMEÇAR?</span>
      <h2>Seu conteúdo pode ir mais longe.</h2>
      <p>Cadastre-se agora e faça parte da Universidade de Afiliados Aquafast.</p>
    </div>
    <div class="cta-action">
      @guest
        <a class="btn btn-white" href="{{ route('register') }}">Quero começar agora <span>→</span></a>
      @else
        <a class="btn btn-white" href="{{ route('app.painel') }}">Ir para minha área <span>→</span></a>
      @endguest
      <small>É gratuito, rápido e sem complicação.</small>
    </div>
  </div>
</section>
</div>
