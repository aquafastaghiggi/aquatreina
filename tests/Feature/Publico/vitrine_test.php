<?php

declare(strict_types=1);

use App\Models\Usuario;

it('mostra o hero, os passos e o programa de premiacao da landing page', function (): void {
    $this->get(route('vitrine'))
        ->assertOk()
        ->assertSee('Aprenda. Crie. Venda. Ganhe.')
        ->assertSee('Quero ser afiliado Aquafast', false)
        ->assertSee('Crie sua conta grátis')
        ->assertSee('Aprenda a divulgar os produtos Aquafast')
        ->assertSee('Poste vídeos com os produtos')
        ->assertSee('Receba comissão por cada venda')
        ->assertSee('Programa de Premiação')
        ->assertSee('Aprendiz')
        ->assertSee('Elite')
        ->assertSee('Quero começar', false);
});

it('mostra ir para minha area em vez do cta de cadastro quando ja autenticado', function (): void {
    $aluno = Usuario::factory()->create();

    $this->actingAs($aluno)->get(route('vitrine'))
        ->assertOk()
        ->assertSee('Ir para minha área')
        ->assertDontSee('Quero ser afiliado Aquafast');
});
