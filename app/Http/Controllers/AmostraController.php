<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\SituacaoAula;
use App\Enums\SituacaoCurso;
use App\Models\Aula;
use App\Models\Curso;
use App\Servicos\Video\FabricaProvedorVideo;
use Illuminate\Contracts\View\View;

class AmostraController extends Controller
{
    public function mostrar(Curso $curso, Aula $aula, FabricaProvedorVideo $provedores): View
    {
        abort_unless($curso->situacao === SituacaoCurso::Publicado, 404);
        abort_unless($aula->modulo->curso_id === $curso->id, 404);
        abort_unless($aula->situacao === SituacaoAula::Publicada && $aula->amostra_gratuita, 404);
        abort_if(blank($aula->video_id), 404);

        $urlEmbed = $provedores->criar($aula->provedor)->urlEmbed($aula->video_id);

        return view('publico.amostra', compact('curso', 'aula', 'urlEmbed'));
    }
}
