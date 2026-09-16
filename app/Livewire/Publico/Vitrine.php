<?php

declare(strict_types=1);

namespace App\Livewire\Publico;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class Vitrine extends Component
{
    public function render(): View
    {
        return view('livewire.publico.vitrine')
            ->layout('components.layouts.publico', ['titulo' => 'Universidade Aquafast — Aprenda. Crie. Venda. Ganhe.']);
    }
}
