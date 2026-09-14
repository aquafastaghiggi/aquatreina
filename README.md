# Aquafast Treina — pacote de especificação

Plataforma de treinamentos para o público externo da Aquafast (distribuidores,
representantes e clientes). Vídeos hospedados no YouTube, curso montado e
acompanhado dentro da plataforma.

Este pacote **não é o projeto**. É a especificação completa para que uma IA
executora construa o projeto, e para que a revisão posterior tenha um contrato
claro do que deveria ter sido feito.

---

## Como usar

### 1. A IA executora (Codex / ChatGPT / Gemini)

Copie o conteúdo deste pacote para a raiz de um diretório vazio e aponte a IA
para ele. O arquivo `AGENTS.md` na raiz é lido automaticamente por Codex,
Gemini CLI e Jules, e é o ponto de entrada de tudo.

Instrução inicial sugerida:

> Leia `AGENTS.md` e siga o fluxo descrito nele. Execute apenas a `etapas/00-fundacao.md`.
> Não avance para a etapa seguinte sem que eu confirme.

**Uma etapa por vez.** Cada arquivo em `etapas/` termina com critérios de
aceite verificáveis. Deixar a IA correr da etapa 0 à 6 numa tacada só é a forma
mais rápida de produzir um projeto que compila e não funciona.

### 2. A validação com Claude Code (VS Code)

Ao final de cada etapa, abra o projeto no VS Code e rode:

> Leia `CLAUDE.md` e `checklists/revisao-claude-code.md`.
> Audite a etapa N contra `etapas/0N-*.md` e `padroes/`.
> Liste divergências por severidade, sem corrigir nada ainda.

`CLAUDE.md` existe exatamente para isso: dá ao Claude Code o contexto do
domínio e o critério de julgamento, sem que ele precise inferir do código.

---

## O que tem aqui

| Pasta | Para quê |
|---|---|
| `AGENTS.md` | Contrato de trabalho da IA executora. Ponto de entrada. |
| `CLAUDE.md` | Contexto de domínio e critério de revisão para o Claude Code. |
| `docs/` | O que o sistema é: domínio, dados, telas, regras, integrações. |
| `padroes/` | Como o código deve ser escrito. Inegociável. |
| `etapas/` | O caminho do zero à entrega, em 7 etapas sequenciais. |
| `artefatos/` | Material pronto: schema SQL, rotas, migrations, comandos, pacotes. |
| `checklists/` | Roteiros de revisão e aceite. |

## Ordem de leitura (humano)

1. `docs/00-visao-geral.md` — o que estamos construindo e por quê
2. `docs/01-decisoes.md` — as escolhas fechadas e as ainda abertas
3. `etapas/00-fundacao.md` — onde o trabalho começa

## Estado

- Versão do pacote: **1.0**
- Data: **14/09/2026**
- Escopo: **MVP** (materiais de apoio + perguntas na aula). Quiz e certificado
  estão em `etapas/99-backlog-pos-mvp.md` e **não entram na v1**.
- Decisões em aberto: ver seção final de `docs/01-decisoes.md`. Nenhuma delas
  bloqueia a etapa 0.
