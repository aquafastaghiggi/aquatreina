<div id="perguntas" class="py-6">
    @if (session('comentario_sucesso'))
        <p class="sucesso mb-5">{{ session('comentario_sucesso') }}</p>
    @endif

    @if ($this->podePerguntar)
        <form wire:submit="enviar" class="rounded-lg border border-linha bg-superficie p-5">
            <label for="corpo-pergunta-{{ $aulaId }}" class="font-medium">Faça uma pergunta sobre esta aula</label>
            <textarea
                id="corpo-pergunta-{{ $aulaId }}"
                wire:model.blur="corpo"
                rows="4"
                maxlength="5000"
                class="mt-3 w-full rounded-md border border-linha bg-fundo px-3 py-2 text-texto"
                placeholder="Escreva sua dúvida com o máximo de contexto possível."
            ></textarea>
            @error('corpo') <p class="mt-2 text-sm text-atencao">{{ $message }}</p> @enderror
            <button type="submit" wire:loading.attr="disabled" class="botao-primario mt-3">Enviar pergunta</button>
        </form>
    @else
        <p class="rounded-lg border border-linha bg-superficie p-4 text-sm text-texto-2">
            O curso está concluído. As perguntas anteriores continuam disponíveis para consulta.
        </p>
    @endif

    <div class="mt-7 space-y-5">
        @forelse ($this->comentarios as $comentario)
            <article wire:key="comentario-{{ $comentario->id }}" class="rounded-lg border border-linha bg-superficie p-5">
                <div class="flex flex-wrap items-center gap-2 text-sm text-texto-3">
                    <strong class="text-texto">{{ $comentario->usuario->nome }}</strong>
                    <span>· {{ $comentario->created_at->diffForHumans() }}</span>
                    @if ($comentario->fixado)<span class="rounded-full bg-marca-suave px-2 py-0.5 text-marca">Fixada</span>@endif
                    @if ($comentario->situacao === \App\Enums\SituacaoComentario::Pendente)
                        <span class="rounded-full bg-atencao/15 px-2 py-0.5 text-atencao">Em análise</span>
                    @endif
                </div>
                <p class="mt-3 whitespace-pre-line text-texto-2">{{ $comentario->corpo }}</p>

                @if ($comentario->respostas->isNotEmpty())
                    <div class="mt-5 space-y-3 border-l-2 border-marca/50 pl-4">
                        @foreach ($comentario->respostas as $resposta)
                            <div wire:key="resposta-{{ $resposta->id }}" class="rounded-md bg-marca-suave p-4">
                                <p class="text-sm font-semibold text-marca">Resposta do instrutor · {{ $resposta->usuario->nome }}</p>
                                <p class="mt-2 whitespace-pre-line text-texto-2">{{ $resposta->corpo }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </article>
        @empty
            <div class="rounded-lg border border-dashed border-linha p-8 text-center">
                <h3 class="font-semibold">Ainda não há perguntas nesta aula.</h3>
                <p class="mt-2 text-sm text-texto-3">Se algo não ficou claro, inaugure este espaço.</p>
            </div>
        @endforelse
    </div>

    @if ($this->comentarios->hasPages())
        <div class="mt-6">{{ $this->comentarios->links() }}</div>
    @endif
</div>
