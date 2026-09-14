# 05 — Integração de vídeo

## Princípio

O resto do sistema **não conhece o YouTube**. Ele conhece `aulas.provedor` e
`aulas.video_id`. Toda tradução disso para um player concreto acontece em
`app/Servicos/Video/`.

```
app/Servicos/Video/
├── ProvedorVideo.php            # interface
├── YoutubeProvedor.php          # implementacao v1
├── ExtratorIdVideo.php          # URL de qualquer formato -> ID
├── LeitorMetadadosVideo.php     # titulo + duracao
└── FabricaProvedorVideo.php     # resolve pelo enum
```

Interface mínima:

```php
interface ProvedorVideo
{
    public function extrairId(string $url): ?string;
    public function metadados(string $videoId): ?MetadadosVideo;   // titulo, duracao_segundos, thumb
    public function urlEmbed(string $videoId): string;
    public function urlThumb(string $videoId): string;
}
```

Quando um curso confidencial exigir outro hospedador (decisão A-01), é uma
classe nova, não um refactor da sala de aula.

---

## Extração do ID

O admin cola **qualquer** formato. O extrator aceita todos:

| Formato | Exemplo |
|---|---|
| watch | `https://www.youtube.com/watch?v=dQw4w9WgXcQ` |
| curto | `https://youtu.be/dQw4w9WgXcQ` |
| embed | `https://www.youtube.com/embed/dQw4w9WgXcQ` |
| shorts | `https://www.youtube.com/shorts/dQw4w9WgXcQ` |
| live | `https://www.youtube.com/live/dQw4w9WgXcQ` |
| só o ID | `dQw4w9WgXcQ` |
| com parâmetros | `...?v=dQw4w9WgXcQ&t=42s&list=PL...` |

ID válido: exatamente 11 caracteres em `[A-Za-z0-9_-]`. Qualquer outra coisa é
erro de validação com mensagem clara — não silêncio.

Isso é o primeiro teste unitário do projeto. Uma tabela de casos, incluindo os
inválidos.

---

## Metadados

**Caminho principal — YouTube Data API v3**

```
GET https://www.googleapis.com/youtube/v3/videos
    ?id={videoId}&part=snippet,contentDetails&key={YOUTUBE_API_KEY}
```

Traz `snippet.title` e `contentDetails.duration` em ISO-8601 (`PT12M15S`), que
vira `duracao_segundos`.

**Fallback — oEmbed, sem chave**

```
GET https://www.youtube.com/oembed?url={url}&format=json
```

Traz título e thumb, **não traz duração**. Nesse caso `duracao_segundos` vira
campo manual obrigatório na aula, com aviso explícito na interface.

**Se ambos falharem:** a aula é salva mesmo assim, com título digitado e duração
manual. Nunca bloqueie o cadastro por indisponibilidade de API externa.

Resultado cacheado por 24h com chave `video:metadados:{provedor}:{id}`.

**Capa:** `https://i.ytimg.com/vi/{id}/maxresdefault.jpg`, com queda para
`hqdefault.jpg`. Um upload a menos por aula.

---

## O player

Embed em `https://www.youtube-nocookie.com/embed/{id}` com:

| Parâmetro | Valor | Por quê |
|---|---|---|
| `rel` | `0` | não sugere vídeo de concorrente ao terminar |
| `modestbranding` | `1` | menos marca do YouTube |
| `playsinline` | `1` | no iPhone, não sequestra a tela inteira |
| `enablejsapi` | `1` | necessário para a IFrame API |
| `origin` | URL do app | exigido pela IFrame API |
| `cc_lang_pref` | `pt` | |

`youtube-nocookie` evita cookie de rastreio antes do consentimento — relevante
para a LGPD (`docs/06-seguranca-e-lgpd.md`).

---

## Rastreamento de progresso

**No navegador** (`resources/js/player-aula.js`, JS puro, fora do Livewire):

1. Carrega a IFrame API e instancia o player.
2. Em `onStateChange = PLAYING`, liga um `setInterval` de `intervalo_ping` (10s).
3. Cada tick envia `POST /app/progresso` com `{aula_id, posicao}`, onde
   `posicao = Math.floor(player.getCurrentTime())`.
4. Em `PAUSED` e `ENDED`, desliga o intervalo e envia um ping final.
5. Em `beforeunload` e `visibilitychange → hidden`, envia via
   `navigator.sendBeacon` — para não perder o último trecho.
6. Ao receber `{concluida: true}`, atualiza o ícone no índice e habilita o botão
   "Próxima aula" sem recarregar a página.

**No servidor:** exatamente a RN-02 de `docs/04-regras-de-negocio.md`.

Resposta do endpoint:

```json
{ "posicao_maxima": 412, "segundos_assistidos": 398, "concluida": false, "percentual_curso": 46 }
```

`throttle:120,1` na rota. Um ping a cada 10s dá 6/min; 120 cobre abas
múltiplas e sobra margem sem virar vetor de abuso.

---

## O que esta integração não resolve

Vídeo **não listado é acessível por qualquer pessoa com o link**, sem login,
fora da plataforma. O `video_id` aparece no HTML da sala de aula; copiar o link
é trivial.

Não existe configuração do YouTube que impeça isso. Para treinamento de produto
o risco é aceitável. Para política comercial, tabela de preço ou margem, não é —
e aí o caminho é um hospedador com link assinado e domínio travado (Bunny
Stream, Panda Video, Vimeo). Por isso o campo é `provedor`, e não `youtube_id`.

**Não prometa sigilo ao cliente interno sobre conteúdo no YouTube.**
