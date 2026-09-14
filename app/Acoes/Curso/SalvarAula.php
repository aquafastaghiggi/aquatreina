<?php

declare(strict_types=1);

namespace App\Acoes\Curso;

use App\Enums\ProvedorVideo as TipoProvedorVideo;
use App\Enums\SituacaoAula;
use App\Eventos\AulaPublicada;
use App\Models\Aula;
use App\Servicos\Video\ProvedorVideo;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class SalvarAula
{
    public function __construct(
        private readonly ProvedorVideo $provedor,
        private readonly Dispatcher $eventos,
    ) {}

    /** @param array<string, mixed> $dados */
    public function executar(Aula $aula, array $dados): Aula
    {
        $eraPublicada = $aula->exists && $aula->situacao === SituacaoAula::Publicada;
        $linkVideo = trim((string) ($dados['link_video'] ?? ''));
        unset($dados['link_video']);

        if ($linkVideo !== '') {
            $videoId = $this->provedor->extrairId($linkVideo);

            if ($videoId === null) {
                throw ValidationException::withMessages(['link_video' => 'Informe um link de vídeo válido.']);
            }

            $dados['provedor'] = TipoProvedorVideo::Youtube;
            $dados['video_id'] = $videoId;
            $metadados = $this->provedor->metadados($videoId);

            if ($metadados !== null) {
                $dados['titulo'] = $metadados->titulo;

                if ($metadados->duracaoSegundos > 0) {
                    $dados['duracao_segundos'] = $metadados->duracaoSegundos;
                }
            }
        }

        $dados['slug'] = $dados['slug'] ?? Str::slug((string) $dados['titulo']);
        $aula->fill($dados)->save();

        if (! $eraPublicada && $aula->situacao === SituacaoAula::Publicada) {
            $aula->forceFill(['publicada_em' => now()])->save();
            $this->eventos->dispatch(new AulaPublicada($aula));
        }

        return $aula->refresh();
    }
}
