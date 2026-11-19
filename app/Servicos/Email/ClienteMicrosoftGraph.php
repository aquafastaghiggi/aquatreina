<?php

declare(strict_types=1);

namespace App\Servicos\Email;

use Illuminate\Contracts\Cache\Factory as FabricaCache;
use Illuminate\Http\Client\Factory as ClienteHttp;
use RuntimeException;

final class ClienteMicrosoftGraph
{
    public function __construct(
        private readonly ClienteHttp $http,
        private readonly FabricaCache $cache,
    ) {}

    /**
     * @param  list<string>  $destinatarios
     */
    public function enviar(array $destinatarios, string $assunto, string $html): void
    {
        $configuracao = $this->configuracao();
        $token = $this->obterToken($configuracao);

        $resposta = $this->http
            ->timeout(15)
            ->withToken($token)
            ->acceptJson()
            ->post(
                'https://graph.microsoft.com/v1.0/users/'.rawurlencode($configuracao['remetente_email']).'/sendMail',
                [
                    'message' => [
                        'subject' => $assunto,
                        'body' => ['contentType' => 'HTML', 'content' => $html],
                        'toRecipients' => array_map(
                            fn (string $email): array => ['emailAddress' => ['address' => $email]],
                            $destinatarios,
                        ),
                    ],
                    'saveToSentItems' => true,
                ],
            );

        $resposta->throw();
    }

    /**
     * @param  array{tenant_id: string, client_id: string, client_secret: string, remetente_email: string}  $configuracao
     */
    private function obterToken(array $configuracao): string
    {
        return $this->cache->store()->remember(
            'microsoft_graph:token:'.sha1($configuracao['client_id']),
            now()->addMinutes(50),
            function () use ($configuracao): string {
                $resposta = $this->http
                    ->timeout(15)
                    ->asForm()
                    ->post(
                        "https://login.microsoftonline.com/{$configuracao['tenant_id']}/oauth2/v2.0/token",
                        [
                            'client_id' => $configuracao['client_id'],
                            'client_secret' => $configuracao['client_secret'],
                            'scope' => 'https://graph.microsoft.com/.default',
                            'grant_type' => 'client_credentials',
                        ],
                    )
                    ->throw();

                $token = $resposta->json('access_token');

                if (! is_string($token) || $token === '') {
                    throw new RuntimeException('O Microsoft Graph não retornou um token de acesso.');
                }

                return $token;
            },
        );
    }

    /**
     * @return array{tenant_id: string, client_id: string, client_secret: string, remetente_email: string}
     */
    private function configuracao(): array
    {
        $configuracao = [
            'tenant_id' => (string) config('services.microsoft_graph.tenant_id'),
            'client_id' => (string) config('services.microsoft_graph.client_id'),
            'client_secret' => (string) config('services.microsoft_graph.client_secret'),
            'remetente_email' => (string) config('services.microsoft_graph.remetente_email'),
        ];

        foreach ($configuracao as $chave => $valor) {
            if ($valor === '') {
                throw new RuntimeException("Configuração ausente do Microsoft Graph: {$chave}.");
            }
        }

        return $configuracao;
    }
}
