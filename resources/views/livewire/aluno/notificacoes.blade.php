<div>
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <a href="{{ route('app.painel') }}" class="link text-sm">← Minha área</a>
            <h1 class="mt-3 text-3xl font-semibold">Notificações</h1>
        </div>
        @if (auth()->user()->unreadNotifications()->exists())
            <button type="button" wire:click="marcarTodasComoLidas" class="botao-secundario text-sm">Marcar todas como lidas</button>
        @endif
    </div>

    <div class="mt-8 space-y-3">
        @forelse ($notificacoes as $notificacao)
            <article wire:key="notificacao-{{ $notificacao->id }}" class="rounded-lg border {{ $notificacao->read_at ? 'border-linha bg-superficie' : 'border-marca/60 bg-marca-suave' }} p-5">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <h2 class="font-semibold">{{ $notificacao->data['titulo'] ?? 'Atualização' }}</h2>
                        <p class="mt-2 text-sm text-texto-2">{{ $notificacao->data['mensagem'] ?? '' }}</p>
                        <p class="mt-2 text-xs text-texto-3">{{ $notificacao->created_at->diffForHumans() }}</p>
                    </div>
                    <div class="flex items-center gap-3 text-sm">
                        @if (! $notificacao->read_at)
                            <button type="button" wire:click="marcarComoLida('{{ $notificacao->id }}')" class="link">Marcar como lida</button>
                        @endif
                        @if (! empty($notificacao->data['url']))
                            <a href="{{ $notificacao->data['url'] }}" class="botao-primario px-4 py-2">Abrir</a>
                        @endif
                    </div>
                </div>
            </article>
        @empty
            <div class="rounded-lg border border-dashed border-linha p-10 text-center">
                <h2 class="text-xl font-semibold">Tudo em dia.</h2>
                <p class="mt-2 text-texto-3">Novas respostas e aulas aparecerão aqui.</p>
            </div>
        @endforelse
    </div>

    @if ($notificacoes->hasPages())
        <div class="mt-7">{{ $notificacoes->links() }}</div>
    @endif
</div>
