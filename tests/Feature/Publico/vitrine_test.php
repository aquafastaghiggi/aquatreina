<?php

declare(strict_types=1);

use App\Models\Usuario;

it('mostra o hero, os beneficios, os passos, os produtos e o programa de premiacao da landing page', function (): void {
    $this->get(route('vitrine'))
        ->assertOk()
        ->assertSee('Transforme')
        ->assertSee('em renda.')
        ->assertSee('Quero ser afiliado Aquafast', false)
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
        ])
        ->assertSee('Aprenda a criar conteúdos, conheça os produtos Aquafast e desenvolva seu potencial como afiliado.')
        ->assertSee('Como funciona?')
        ->assertSee('Crie sua conta gratuitamente')
        ->assertSee('Aprenda com nossos conteúdos')
        ->assertSee('Poste seus vídeos')
        ->assertSee('Ganhe comissão')
        ->assertSee('Conteúdo prático, conhecimento sobre os produtos e tudo o que você precisa para evoluir como afiliado Aquafast.')
        ->assertSee('Conhecer a Universidade', false)
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
        ->assertSee('Aprendiz')
        ->assertSee('Elite')
        ->assertSee('R$ 1 milhão')
        ->assertSee('Seu conteúdo pode ir mais longe.')
        ->assertSee('Quero começar agora', false)
        ->assertDontSee('Dúvidas')
        ->assertDontSee('Mais do que um curso');
});

it('mostra ir para minha area em vez do cta de cadastro quando ja autenticado', function (): void {
    $aluno = Usuario::factory()->create();

    $this->actingAs($aluno)->get(route('vitrine'))
        ->assertOk()
        ->assertSee('Ir para minha área')
        ->assertDontSee('Quero ser afiliado Aquafast');
});
