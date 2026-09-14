<?php

declare(strict_types=1);

namespace App\Livewire\Aluno;

use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class Notificacoes extends Component
{
    use WithPagination;

    public function marcarComoLida(string $id): void
    {
        $notificacao = auth()->user()->notifications()->findOrFail($id);
        $notificacao->markAsRead();
    }

    public function marcarTodasComoLidas(): void
    {
        auth()->user()->unreadNotifications->markAsRead();
    }

    public function render(): View
    {
        return view('livewire.aluno.notificacoes', [
            'notificacoes' => auth()->user()->notifications()->latest()->paginate(15),
        ])->layout('components.layouts.aluno', ['titulo' => 'Notificações']);
    }
}
