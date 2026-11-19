<?php

declare(strict_types=1);

use App\Models\Usuario;
use App\Notifications\CadastroAprovado;
use App\Servicos\Email\ClienteMicrosoftGraph;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;

it('envia a notificacao pelo graph usando o remetente aquafast', function (): void {
    config()->set('services.microsoft_graph', [
        'tenant_id' => 'tenant-teste',
        'client_id' => 'client-teste',
        'client_secret' => 'segredo-teste',
        'remetente_email' => 'aquafast@aquafast.com.br',
        'admin_email' => 'aquafast@aquafast.com.br',
    ]);

    Cache::forget('microsoft_graph:token:'.sha1('client-teste'));

    Http::fake([
        'https://login.microsoftonline.com/*' => Http::response(['access_token' => 'token-teste'], 200),
        'https://graph.microsoft.com/*' => Http::response('', 202),
    ]);

    app(ClienteMicrosoftGraph::class)->enviar(
        ['aluno@example.test'],
        'Cadastro aprovado',
        '<p>Seu acesso foi liberado.</p>',
    );

    Http::assertSent(fn (Request $requisicao): bool => $requisicao->url() === 'https://graph.microsoft.com/v1.0/users/aquafast%40aquafast.com.br/sendMail'
        && $requisicao->hasHeader('Authorization', 'Bearer token-teste')
        && $requisicao['message']['subject'] === 'Cadastro aprovado'
        && $requisicao['message']['toRecipients'][0]['emailAddress']['address'] === 'aluno@example.test');
});

it('entrega a notificacao de aprovacao pelo canal graph', function (): void {
    config()->set('services.microsoft_graph', [
        'tenant_id' => 'tenant-canal',
        'client_id' => 'client-canal',
        'client_secret' => 'segredo-canal',
        'remetente_email' => 'aquafast@aquafast.com.br',
        'admin_email' => 'aquafast@aquafast.com.br',
    ]);

    Cache::forget('microsoft_graph:token:'.sha1('client-canal'));

    Http::fake([
        'https://login.microsoftonline.com/*' => Http::response(['access_token' => 'token-canal'], 200),
        'https://graph.microsoft.com/*' => Http::response('', 202),
    ]);

    $usuario = Usuario::factory()->create([
        'nome' => 'Maria Teste',
        'email' => 'maria@example.test',
    ]);

    Notification::sendNow($usuario, new CadastroAprovado($usuario->nome));

    Http::assertSent(fn (Request $requisicao): bool => str_starts_with($requisicao->url(), 'https://graph.microsoft.com/')
        && $requisicao['message']['subject'] === 'Seu cadastro foi aprovado na Universidade Aquafast'
        && str_contains($requisicao['message']['body']['content'], 'Olá, Maria Teste!')
        && $requisicao['message']['toRecipients'][0]['emailAddress']['address'] === 'maria@example.test');
});
