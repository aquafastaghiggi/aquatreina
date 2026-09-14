# 06 — Git, commits e entrega

## Branches

```
main            sempre estável, espelha produção
desenvolvimento integração
etapa/0-fundacao, etapa/1-dominio, ...
correcao/<assunto-curto>
```

Uma branch por etapa. Merge em `desenvolvimento` só depois de a etapa passar
nos critérios de aceite dela.

## Commits

Conventional Commits, descrição em português, imperativo, minúscula, sem ponto.

```
feat(curso): adiciona construtor de curriculo com arrastar e soltar
fix(progresso): ignora salto de barra ao somar tempo assistido
test(aula): cobre os cinco cenarios da RN-02
chore(deps): adiciona spatie/laravel-activitylog
docs(padroes): esclarece o ajuste da tabela usuarios
refactor(video): isola o youtube atras de ProvedorVideo
```

Escopos: `curso`, `aula`, `matricula`, `progresso`, `comentario`, `material`,
`usuario`, `admin`, `video`, `infra`.

Regras:

- Um commit, uma ideia. Nada de "ajustes gerais".
- Commit que quebra `php artisan test` não entra.
- Migration e model que a acompanha no mesmo commit.
- Mensagem cita a regra quando aplicável: `fix(progresso): ... (RN-02)`.

## Ao fim de cada etapa

1. `vendor/bin/pint`
2. `php artisan test` — verde
3. Commit final da etapa
4. Relatório no formato de `AGENTS.md` §3
5. Parar e esperar validação

## O que nunca vai para o repositório

- `.env`
- `storage/app/materiais/**` (arquivos reais de aluno)
- `public/build`, `node_modules`, `vendor`
- dump de banco com dado real
- chave, token ou senha, inclusive em teste

`.gitignore` conferido ainda na etapa 0.

## Mensagem de PR

```
## O que mudou
## Etapa e critérios atendidos
## Como testar
## Divergências da spec
```

A última seção é a que interessa na revisão com o Claude Code.
