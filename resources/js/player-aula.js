const carregarApiYoutube = () => {
    if (window.YT?.Player) return Promise.resolve(window.YT);
    if (window.apiYoutubePromise) return window.apiYoutubePromise;

    window.apiYoutubePromise = new Promise((resolve) => {
        const callbackAnterior = window.onYouTubeIframeAPIReady;
        window.onYouTubeIframeAPIReady = () => {
            if (typeof callbackAnterior === 'function') callbackAnterior();
            resolve(window.YT);
        };

        if (!document.querySelector('script[src="https://www.youtube.com/iframe_api"]')) {
            const script = document.createElement('script');
            script.src = 'https://www.youtube.com/iframe_api';
            document.head.appendChild(script);
        }
    });

    return window.apiYoutubePromise;
};

const iniciarPlayer = async (sala) => {
    if (sala.dataset.playerIniciado === '1') return;
    sala.dataset.playerIniciado = '1';

    const YT = await carregarApiYoutube();
    const iframe = sala.querySelector('iframe');
    const botaoRetomar = sala.querySelector('[data-retomar]');
    const endpoint = sala.dataset.endpoint;
    const aulaId = Number(sala.dataset.aulaId);
    const intervaloSegundos = Number(sala.dataset.intervalo);
    const posicaoSalva = Number(sala.dataset.posicao);
    let intervalo = null;
    let concluida = sala.dataset.concluida === '1';
    let playerPronto = false;
    let player;

    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const posicaoAtual = () => playerPronto ? Math.floor(player.getCurrentTime()) : posicaoSalva;

    const enviar = async (usarBeacon = false) => {
        if (concluida || !playerPronto) return;
        const dados = { aula_id: aulaId, posicao: posicaoAtual() };

        if (usarBeacon && navigator.sendBeacon) {
            const corpo = new Blob([JSON.stringify({ ...dados, _token: csrf })], { type: 'application/json' });
            navigator.sendBeacon(endpoint, corpo);
            return;
        }

        let resposta;

        try {
            resposta = await fetch(endpoint, {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, Accept: 'application/json' },
                body: JSON.stringify(dados),
            });
        } catch {
            return;
        }

        if (!resposta.ok) return;
        const progresso = await resposta.json();

        if (progresso.concluida) {
            concluida = true;
            clearInterval(intervalo);
            window.Livewire?.dispatch('progresso-atualizado', { percentual_curso: progresso.percentual_curso });
        }
    };

    const parar = () => {
        clearInterval(intervalo);
        intervalo = null;
    };

    player = new YT.Player(iframe, {
        events: {
            onReady: () => { playerPronto = true; },
            onStateChange: (evento) => {
                if (evento.data === YT.PlayerState.PLAYING && !intervalo) {
                    intervalo = setInterval(() => enviar(), intervaloSegundos * 1000);
                }

                if (evento.data === YT.PlayerState.PAUSED || evento.data === YT.PlayerState.ENDED) {
                    parar();
                    enviar();
                }
            },
        },
    });

    botaoRetomar?.addEventListener('click', () => {
        player.seekTo(posicaoSalva, true);
        player.playVideo();
        botaoRetomar.remove();
    });

    window.addEventListener('beforeunload', () => enviar(true));
    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'hidden') enviar(true);
    });
};

const iniciarPlayersDaPagina = () => {
    document.querySelectorAll('[data-player-aula]').forEach(iniciarPlayer);
};

document.addEventListener('DOMContentLoaded', iniciarPlayersDaPagina);
document.addEventListener('livewire:navigated', iniciarPlayersDaPagina);
