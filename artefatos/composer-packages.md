# Dependências

Instale exatamente isto. Qualquer acréscimo é divergência a reportar.

## Produção

```bash
composer require filament/filament:"^4.0"
composer require laravel/fortify
composer require spatie/laravel-permission
composer require spatie/laravel-activitylog
composer require maatwebsite/excel
composer require guzzlehttp/guzzle
```

| Pacote | Para quê | Onde aparece |
|---|---|---|
| `filament/filament` | painel admin | etapa 0 |
| `laravel/fortify` | login, cadastro, reset, verificação | etapa 0 |
| `spatie/laravel-permission` | papéis `aluno`/`instrutor`/`admin` | etapa 0 |
| `spatie/laravel-activitylog` | auditoria | etapa 0 |
| `maatwebsite/excel` | exportação e importação | etapa 5 |
| `guzzlehttp/guzzle` | YouTube Data API v3 | etapa 1 |

## Desenvolvimento

```bash
composer require --dev pestphp/pest pestphp/pest-plugin-laravel
composer require --dev laravel/pint
composer require --dev barryvdh/laravel-debugbar
```

Debugbar **sai** das dependências de produção na etapa 6.

## Front-end

```bash
npm install -D tailwindcss @tailwindcss/vite
npm install sortablejs
```

`sortablejs` serve ao construtor de currículo (etapa 1).
A IFrame API do YouTube é carregada por `<script>` direto, sem pacote npm.

## Deliberadamente fora

| Pacote | Por quê |
|---|---|
| `laravel/jetstream` | Fortify sozinho resolve, sem arrastar Inertia/Teams |
| `laravel/scout` + Meilisearch | busca por `LIKE` atende o volume da v1 (ver B-09) |
| `spatie/laravel-medialibrary` | upload simples com `Storage`; medialibrary é peso extra para capa e PDF |
| `predis` / Redis | fila `database` atende; Redis quando o volume exigir |
| serviço de CAPTCHA | honeypot + throttle resolvem, e evitam mais um processador de dados na LGPD |
| SDK do Google | duas chamadas HTTP não justificam o SDK inteiro |

Se alguma dessas escolhas se mostrar errada durante a execução, **reporte como
divergência** antes de instalar.
