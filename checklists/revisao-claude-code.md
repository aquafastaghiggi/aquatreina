# Roteiro de revisão — Claude Code no VS Code

Use ao fim de cada etapa. O objetivo é achar divergência entre o que foi
especificado e o que foi construído, **antes** de a próxima etapa se apoiar em
cima.

## Prompt inicial

> Leia `CLAUDE.md` e este arquivo.
> Audite a etapa N contra `etapas/0N-*.md` e `padroes/`.
> Liste as divergências por severidade. Não corrija nada ainda.

---

## 1. Verificação executável (antes de ler código)

```bash
composer install
php artisan migrate:fresh --seed     # tem que rodar limpo
vendor/bin/pint --test               # sem apontamento
php artisan test                     # verde
php artisan route:list               # confere com artefatos/rotas.md
```

Critério de aceite se verifica **executando**, não lendo. Uma lista de checks
marcados sem execução não vale nada.

## 2. Banco

- [ ] Tabelas, colunas e tipos batem com `artefatos/schema.sql`
- [ ] `UNIQUE (usuario_id, curso_id)` em `matriculas` existe
- [ ] `UNIQUE (matricula_id, aula_id)` em `progresso_aulas` existe
- [ ] Valores de enum batem exatamente (`publicado`, não `ativo`)
- [ ] Nenhuma coluna do tipo `ENUM` do MySQL
- [ ] `ON DELETE` conforme a tabela de política em `artefatos/migrations.md`
- [ ] Nenhuma referência residual à tabela `users`
- [ ] Nenhuma migration antiga reescrita (confira o histórico do git)

## 3. Arquitetura

- [ ] Regra de negócio em `app/Acoes/`, não em controller nem em componente Livewire
- [ ] Nenhuma menção a "youtube" fora de `app/Servicos/Video/`
- [ ] Enums PHP com cast; nenhuma string de situação comparada com `==`
- [ ] `$fillable` explícito; nenhum `$guarded = []`
- [ ] Nenhum `env()` fora de `config/`
- [ ] Nenhum número mágico: tudo em `config/treina.php`

## 4. Segurança

- [ ] Toda rota nova com middleware adequado
- [ ] Toda ação sensível com Policy verificada **no backend**
- [ ] Acesso à aula validado pelo caminho `aula → modulo → curso`, não pelo slug da URL
- [ ] Materiais fora de `public/`; URL direta no storage não funciona
- [ ] Upload valida MIME real, não extensão
- [ ] Throttles de `artefatos/rotas.md` aplicados
- [ ] Nenhuma chave, token ou senha versionada

## 5. Regras de negócio

Para cada RN tocada pela etapa, **rode o cenário**, não confie no teste:

- [ ] RN-01 — conclusão automática aos 90% e manual
- [ ] RN-02 — os cinco cenários, com atenção ao salto de barra
- [ ] RN-03 — aula em rascunho dá 404 por URL direta
- [ ] RN-04 — publicar aula nova reabre a matrícula concluída
- [ ] RN-05 — inscrição idempotente
- [ ] RN-06 — moderação respeitada
- [ ] RN-07 — download sem matrícula dá 403
- [ ] RN-08 — situação da conta bloqueia acesso

## 6. Desempenho

- [ ] Nenhum N+1 em `/app`, `/app/catalogo` e na sala de aula
  (ligue o Debugbar ou `DB::listen` e conte as queries)
- [ ] Ping de progresso **não** passa por Livewire
- [ ] Listagens paginadas
- [ ] `percentual_progresso` lido do cache, não somado em tempo de render

## 7. Interface

- [ ] Funciona a 400px
- [ ] Na sala de aula em celular, o índice fica **abaixo** do player
- [ ] Estados vazios implementados
- [ ] Foco de teclado visível
- [ ] Nenhum texto em inglês na tela
- [ ] Estado de aula não comunicado só por cor

## 8. Testes

- [ ] Os testes mínimos da etapa existem e citam o código da regra
- [ ] Nenhum teste dependente de ordem de execução
- [ ] Nenhuma chamada de rede real (`Http::fake()` na API do YouTube)
- [ ] Nenhum `sleep()`

---

## Formato do relatório

```
## Revisão — Etapa N

### Bloqueia entrega
- [arquivo:linha] <o quê> — <por que bloqueia> — <regra ou padrão violado>

### Corrigir antes da próxima etapa
- ...

### Melhoria
- ...

### Divergências de especificação
- <onde a implementação divergiu e a spec parece estar errada, não o código>
```

A última seção importa: nem toda divergência é bug da IA executora. Às vezes a
especificação é que estava errada, e o pacote é que precisa mudar.

## O que não fazer

- Não corrija antes de apresentar a lista.
- Não reescreva arquitetura por preferência pessoal se o padrão foi seguido.
- Não aprove por leitura de código quando o critério pede execução.
