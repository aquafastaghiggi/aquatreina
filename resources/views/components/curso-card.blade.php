@props(['curso', 'capaUrl' => null, 'inscrito' => false, 'percentual' => null, 'iniciarDireto' => false, 'videosEmbed' => []])

<article class="overflow-hidden rounded-lg border border-linha bg-superficie">
    @if (! empty($videosEmbed))
        {{-- Vídeo embutido direto no card, sem link e sem interação de navegação. --}}
        @include('components.curso-card-conteudo', compact('capaUrl', 'inscrito', 'curso', 'percentual', 'videosEmbed'))
    @elseif ($iniciarDireto && ! $inscrito)
        {{-- Sem matrícula ainda: inscreve e já cai direto na aula, sem passar pela página do curso. --}}
        <form method="POST" action="{{ route('app.inscrever', $curso) }}">
            @csrf
            <button type="submit" class="group block w-full text-left focus-ring">
                @include('components.curso-card-conteudo', compact('capaUrl', 'inscrito', 'curso', 'percentual'))
            </button>
        </form>
    @else
        <a href="{{ $inscrito ? route('app.curso', $curso) : route('cursos.mostrar', $curso) }}" class="group block focus-ring">
            @include('components.curso-card-conteudo', compact('capaUrl', 'inscrito', 'curso', 'percentual'))
        </a>
    @endif
</article>
