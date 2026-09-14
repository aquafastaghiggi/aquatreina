<?php

declare(strict_types=1);

namespace App\Livewire\Publico;

use App\Models\Categoria;
use App\Suporte\ApresentacaoCurso;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class Vitrine extends Component
{
    public function render(ApresentacaoCurso $apresentacao): View
    {
        $categorias = Cache::remember('catalogo:publicados', now()->addMinutes(10), fn () => Categoria::query()
            ->whereHas('cursos', fn ($consulta) => $consulta->publicados())
            ->with(['cursos' => fn ($consulta) => $consulta->publicados()
                ->with(['categoria', 'modulos.aulas'])
                ->orderBy('posicao')])
            ->orderBy('posicao')
            ->get());

        return view('livewire.publico.vitrine', compact('categorias', 'apresentacao'))
            ->layout('components.layouts.publico', ['titulo' => 'Aquafast Treina']);
    }
}
