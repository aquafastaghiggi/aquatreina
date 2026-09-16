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
            ->layout('components.layouts.aquafast-landing', ['titulo' => 'Universidade Aquafast — Transforme seu conteúdo em renda.']);
    }
}
