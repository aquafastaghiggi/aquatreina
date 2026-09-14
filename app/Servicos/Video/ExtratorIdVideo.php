<?php

declare(strict_types=1);

namespace App\Servicos\Video;

final class ExtratorIdVideo
{
    private const PADRAO_ID = '/^[A-Za-z0-9_-]{11}$/';

    public function extrair(string $entrada): ?string
    {
        $entrada = trim($entrada);

        if (preg_match(self::PADRAO_ID, $entrada) === 1) {
            return $entrada;
        }

        $partes = parse_url($entrada);

        if (! is_array($partes) || ! isset($partes['host'])) {
            return null;
        }

        $host = strtolower($partes['host']);
        $caminho = trim($partes['path'] ?? '', '/');
        $id = null;

        if (in_array($host, ['youtu.be', 'www.youtu.be'], true)) {
            $id = explode('/', $caminho)[0] ?? null;
        }

        if (in_array($host, ['youtube.com', 'www.youtube.com', 'm.youtube.com'], true)) {
            parse_str($partes['query'] ?? '', $consulta);
            $segmentos = explode('/', $caminho);

            $id = $consulta['v'] ?? match ($segmentos[0] ?? '') {
                'embed', 'shorts', 'live' => $segmentos[1] ?? null,
                default => null,
            };
        }

        return is_string($id) && preg_match(self::PADRAO_ID, $id) === 1 ? $id : null;
    }
}
