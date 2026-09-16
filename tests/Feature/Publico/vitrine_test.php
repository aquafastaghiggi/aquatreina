<?php

declare(strict_types=1);

use App\Models\Usuario;

it('mostra o hero, os passos, os produtos e o programa de premiacao da landing page', function (): void {
    $this->get(route('vitrine'))
        ->assertOk()
        ->assertSee('Transforme')
        ->assertSee('em renda.')
        ->assertSee('Quero ser afiliado Aquafast', false)
        ->assertSee('Como funciona?')
        ->assertSee('Crie sua conta gratuitamente')
        ->assertSee('Aprenda com nossos conteúdos')
        ->assertSee('Poste seus vídeos')
        ->assertSee('Ganhe comissão')
        ->assertSee('Produtos que')
        ->assertSee('LIMPEZA')
        ->assertSee('KITS ESPECIAIS')
        ->assertSee('Mais do que um curso,')
        ->assertSee('Você cria. Você vende.')
        ->assertSee('Aprendiz')
        ->assertSee('Elite')
        ->assertSee('R$ 1 milhão')
        ->assertSee('Seu conteúdo pode ir mais longe.')
        ->assertSee('Quero começar agora', false);
});

it('mostra ir para minha area em vez do cta de cadastro quando ja autenticado', function (): void {
    $aluno = Usuario::factory()->create();

    $this->actingAs($aluno)->get(route('vitrine'))
        ->assertOk()
        ->assertSee('Ir para minha área')
        ->assertDontSee('Quero ser afiliado Aquafast');
});
