<?php

declare(strict_types=1);

use App\Models\Usuario;

it('mostra o hero, os beneficios, os passos, os produtos e o programa de premiacao da landing page', function (): void {
    $this->get(route('vitrine'))
        ->assertOk()
        ->assertSee('Transforme')
        ->assertSee('em renda.')
        ->assertSee('PROGRAMA GRATUITO · TIKTOK SHOP')
        ->assertSee('Criar conta grátis', false)
        ->assertSee('Entrar')
        ->assertSeeInOrder([
            'Tudo para você começar e evoluir',
            'APRENDA',
            'VENDA',
            'EVOLUA',
            'Como funciona?',
            'Uma Universidade feita para você crescer',
            'TREINAMENTOS PASSO A PASSO',
            'CONHEÇA OS PRODUTOS',
            'CONTEÚDOS PARA AFILIADOS',
            'ACOMPANHE SUA EVOLUÇÃO',
            'CRIE. DEMONSTRE. COMPARTILHE.',
            'Produtos que viram conteúdo',
            'Você cria. Você vende.',
            'PERGUNTAS FREQUENTES',
            'Ficou com alguma dúvida?',
            'Seu conteúdo pode ir mais longe.',
        ])
        ->assertSee('Aprenda a criar conteúdos, conheça os produtos Aquafast e desenvolva seu potencial como afiliado.')
        ->assertSee('Como funciona?')
        ->assertSee('Crie sua conta gratuitamente')
        ->assertSee('Cadastre-se na Universidade Aquafast.')
        ->assertSee('Aprenda com nossos conteúdos')
        ->assertSee('Poste seus vídeos')
        ->assertSee('Ganhe comissão')
        ->assertSee('Conteúdo prático, conhecimento sobre os produtos e tudo o que você precisa para evoluir como afiliado Aquafast.')
        ->assertSee('universidade-area-logada.webp', false)
        ->assertSee('Demonstre resultados, mostre aplicações reais e transforme os produtos Aquafast em conteúdos que ajudam sua audiência e geram oportunidades de venda.')
        ->assertSee('DEMONSTRE O RESULTADO')
        ->assertSee('Mostre o produto em uso e apresente de forma clara o resultado da limpeza.')
        ->assertSee('MOSTRE COMO USAR')
        ->assertSee('Crie conteúdos simples ensinando onde e como utilizar cada produto.')
        ->assertSee('COMPARTILHE SUA EXPERIÊNCIA')
        ->assertSee('Mostre situações reais de uso e apresente os produtos de forma natural para sua audiência.')
        ->assertSee('Não sabe o que gravar?')
        ->assertSee('Dentro da Universidade você encontra treinamentos e orientações para começar a produzir seus conteúdos.')
        ->assertSee('Ver treinamentos', false)
        ->assertSee('A Aquafast poderá liberar amostras reembolsáveis, mediante avaliação dos perfis que estejam alinhados à nossa marca.')
        ->assertSee('products-brand-mark', false)
        ->assertSee('produtos/frascos/aromatizador-oriental.webp', false)
        ->assertSee('produtos/frascos/home-spray-oriental.webp', false)
        ->assertSee('produtos/frascos/amaciante-oriental.webp', false)
        ->assertSee('produtos/frascos/multiuso-alcool-bicarbonato.webp', false)
        ->assertSee('produtos/frascos/lava-roupas-total-clean.webp', false)
        ->assertSee('produtos/frascos/desengordurante-cozinha-limao.webp', false)
        ->assertSee('produtos/frascos/multiuso-poder-o2.webp', false)
        ->assertSee('Você cria. Você vende.')
        ->assertSee('Programa piloto por 90 dias, com início previsto para 30/10/2026 (a princípio).')
        ->assertSee('Os 8% de comissão valem em todos os níveis — o reconhecimento de cada nível é um benefício adicional, não substitui a comissão.', false)
        ->assertSee('Aprendiz')
        ->assertSee('Elite')
        ->assertSee('Montevidéu')
        ->assertSee('iPhone 18 Pro + Viagem especial')
        ->assertSee('R$ 1 milhão')
        ->assertSee('Começar minha jornada', false)
        ->assertSee('Confira as respostas para as principais dúvidas sobre a Universidade Aquafast e o Programa de Afiliados Aquafast.')
        ->assertSee('A Universidade Aquafast é gratuita?')
        ->assertSee('Sim. O acesso à Universidade Aquafast e aos treinamentos disponíveis para afiliados é gratuito.')
        ->assertSee('Preciso ter muitos seguidores para participar?')
        ->assertSee('Não é necessário ter uma grande audiência, mas você precisa ter o TikTok Shop ativo na sua conta, com permissão para divulgar produtos como afiliado. O mais importante é criar conteúdos autênticos, úteis e consistentes.')
        ->assertSee('Preciso comprar produtos para começar?')
        ->assertSee('A página não informa compra obrigatória para começar. O que está definido é que a Universidade ensina você a conhecer os produtos e que a Aquafast poderá liberar amostras reembolsáveis, mediante avaliação dos perfis que estejam alinhados à nossa marca.')
        ->assertSee('Como funcionam as amostras de produtos?')
        ->assertSee('O que vou encontrar dentro da Universidade?')
        ->assertSee('Você terá acesso a treinamentos, conteúdos sobre os produtos Aquafast e materiais desenvolvidos para ajudar na sua evolução como afiliado.')
        ->assertSee('Como funcionam as comissões?')
        ->assertSee('As comissões seguem as condições vigentes do Programa de Afiliados Aquafast e do TikTok Shop.')
        ->assertSee('Como funciona o Programa de Premiação?')
        ->assertSee('O programa possui diferentes níveis de evolução. Conforme os critérios de cada nível são alcançados, novas conquistas e premiações são desbloqueadas. Consulte a jornada apresentada acima para conhecer os níveis e requisitos.')
        ->assertSee('<details name="faq-home" class="faq-item">', false)
        ->assertSee('Seu conteúdo pode ir mais longe.')
        ->assertDontSee('Universidade de Afiliados Aquafast')
        ->assertDontSee('Quero ser afiliado Aquafast')
        ->assertDontSee('Quero começar agora', false)
        ->assertDontSee('Montevidéo')
        ->assertDontSee('Viagem (a decidir)')
        ->assertDontSee('Mais do que um curso');
});

it('mostra ir para minha area em vez do cta de cadastro quando ja autenticado', function (): void {
    $aluno = Usuario::factory()->create();

    $this->actingAs($aluno)->get(route('vitrine'))
        ->assertOk()
        ->assertSee('Minha área')
        ->assertSee('Começar minha jornada')
        ->assertDontSee('Quero ser afiliado Aquafast')
        ->assertDontSee('Criar conta grátis', false);
});
