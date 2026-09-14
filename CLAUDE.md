# CLAUDE.md

Contexto para revisão do **Aquafast Treina** no VS Code. Este projeto foi
implementado por outra IA a partir da especificação em `docs/`, `padroes/` e
`etapas/`. Seu papel aqui é **auditar**, não reescrever por conta própria.

## O que é o sistema

Plataforma de treinamentos da Aquafast (indústria de água mineral e bebidas)
para público **externo**: distribuidores, representantes e clientes. O aluno se
cadastra sozinho, escolhe um curso no catálogo aberto, assiste às aulas (vídeo
no YouTube, embed), baixa materiais e faz perguntas respondidas pelo instrutor.
O admin monta cursos, modera perguntas e acompanha progresso.

Não é LMS de compliance interno. Não há trilha obrigatória, prazo ou integração
com RH.

## Stack

Laravel 12 · PHP 8.3 · Livewire 3 · Filament 4 (`/admin`) · MySQL 8 · Pest · Pint.

## Convenções deste projeto

- **Domínio em português sem acento**: `cursos`, `aulas`, `matriculas`,
  `progresso_aulas`, `comentarios`, `materiais`, `usuarios`, `organizacoes`.
- **API do framework em inglês**: `created_at`, `up()`, `render()`, `handle()`.
- A tabela de autenticação é `usuarios` (não `users`), com model `Usuario`.
- Regra de negócio vive em `app/Acoes/` (uma classe, um método `executar`).
  Controller e componente Livewire só orquestram.
- Integração com vídeo fica isolada em `app/Servicos/Video/`. O resto do código
  não conhece o YouTube.

## Onde estão as respostas

| Pergunta | Arquivo |
|---|---|
| O que o sistema faz | `docs/00-visao-geral.md` |
| Por que foi decidido assim | `docs/01-decisoes.md` |
| Schema exato | `docs/02-modelo-de-dados.md`, `artefatos/schema.sql` |
| Rotas e telas | `docs/03-mapa-de-telas-e-rotas.md` |
| Regras de negócio | `docs/04-regras-de-negocio.md` |
| Progresso de vídeo | `docs/05-integracao-youtube.md` |
| Segurança e LGPD | `docs/06-seguranca-e-lgpd.md` |
| Como o código deve ser | `padroes/` |
| O que era pra estar pronto | `etapas/` |

## Como revisar

Use `checklists/revisao-claude-code.md`. O roteiro curto:

1. Identifique a etapa concluída e abra `etapas/0N-*.md`.
2. Verifique cada critério de aceite **executando**, não lendo.
3. Confronte o código com `padroes/`.
4. Rode `vendor/bin/pint --test` e `php artisan test`.
5. Reporte por severidade: **Bloqueia entrega** / **Corrigir antes da próxima
   etapa** / **Melhoria**.

Não corrija nada antes de apresentar a lista. Divergência de spec e bug são
coisas diferentes: a primeira pode significar que a spec estava errada.

## Pontos de atenção recorrentes

- Progresso de vídeo aceito direto do cliente sem validação de salto.
- `percentual_progresso` recalculado por query dentro de loop de render.
- Material de apoio acessível por URL pública.
- Autorização checada só na view, sem Policy no backend.
- Enum divergente do schema (`publicado` virou `ativo`, e afins).
- N+1 em `/app` e no catálogo.
