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
        ])
        ->assertSee('Aprenda a criar conteúdos, conheça os produtos Aquafast e desenvolva seu potencial como afiliado.')
        ->assertSee('Como funciona?')
        ->assertSee('Crie sua conta gratuitamente')
        ->assertSee('Aprenda com nossos conteúdos')
        ->assertSee('Poste seus vídeos')
        ->assertSee('Ganhe comissão')
        ->assertSee('Produtos que')
        ->assertSee('produtos-linha.webp', false)
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
