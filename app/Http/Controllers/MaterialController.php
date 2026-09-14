<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MaterialController extends Controller
{
    public function baixar(Material $material): StreamedResponse
    {
        $material->loadMissing('aula.modulo.curso');
        Gate::authorize('download', $material);
        $disco = Storage::disk($material->disco);
        abort_unless($disco->exists($material->caminho), 404);

        $material->increment('total_downloads');
        $extensao = pathinfo($material->caminho, PATHINFO_EXTENSION);
        $nome = Str::endsWith(Str::lower($material->titulo), ".{$extensao}")
            ? $material->titulo
            : "{$material->titulo}.{$extensao}";

        return $disco->download($material->caminho, $nome);
    }
}
