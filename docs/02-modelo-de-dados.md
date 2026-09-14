# 02 — Modelo de dados

O SQL executável está em `artefatos/schema.sql`. A ordem das migrations está em
`artefatos/migrations.md`. Este documento explica **o porquê** de cada tabela.

Convenções em `padroes/04-banco-e-migrations.md`. Resumo: domínio em português
sem acento, `created_at`/`updated_at`/`deleted_at` em inglês (framework), chave
estrangeira `<tabela_singular>_id`, enum como `string` + cast de Enum PHP.

---

## Diagrama de relacionamento

```
organizacoes 1──n usuarios
categorias   1──n cursos
usuarios     1──n cursos            (responsavel_id — instrutor dono)
cursos       1──n modulos  1──n aulas  1──n materiais
                                       1──n comentarios ──n (auto-relacao: comentario_pai_id)
usuarios     1──n matriculas  n──1 cursos      [unique(usuario_id, curso_id)]
matriculas   1──n progresso_aulas  n──1 aulas  [unique(matricula_id, aula_id)]
matriculas   n──1 aulas            (ultima_aula_id — "continue de onde parou")
```

---

## Tabelas

### `usuarios`

Autenticação e cadastro. Substitui a `users` padrão do Laravel — ver
`padroes/04-banco-e-migrations.md` §2 para o ajuste no framework.

| Coluna | Tipo | Nota |
|---|---|---|
| `id` | bigint PK | |
| `nome` | string(160) | |
| `email` | string(190) unique | |
| `email_verified_at` | timestamp null | nome do framework, mantido |
| `password` | string | nome do framework, mantido |
| `telefone` | string(20) null | |
| `empresa` | string(160) null | texto livre, preenchido pelo aluno |
| `cargo` | string(120) null | |
| `organizacao_id` | FK null | vínculo formal, atribuído pelo admin |
| `situacao` | string(20) | `pendente` · `ativo` · `bloqueado` |
| `avatar_caminho` | string null | |
| `ultimo_acesso_em` | timestamp null | atualizado no máximo 1x/hora |
| `termos_aceitos_em` | timestamp null | LGPD |
| `termos_ip` | string(45) null | LGPD, suporta IPv6 |
| `remember_token` | string(100) null | framework |

`empresa` é texto livre e `organizacao_id` é o vínculo real. O aluno digita
"Distrib. Sul"; o admin depois liga ao registro certo. Não force o aluno a
escolher numa lista que ele não conhece.

### `organizacoes`

Empresa do aluno. Existe desde a v1 mesmo sem tela dedicada, porque
retrofitar vínculo em base já povoada é trabalhoso.

`id` · `nome` · `cnpj` null · `tipo` (`distribuidor`·`representante`·`cliente`) ·
`uf` char(2) null · `ativa` bool

### `categorias`

Trilha temática do catálogo. `id` · `nome` · `slug` unique · `cor` char(7) null ·
`posicao` int.

### `cursos`

| Coluna | Tipo | Nota |
|---|---|---|
| `categoria_id` | FK null | |
| `responsavel_id` | FK usuarios | instrutor dono; base da Policy |
| `titulo` · `slug` unique · `subtitulo` null | | |
| `descricao` | longtext null | rich text |
| `capa_caminho` | string null | se vazio, usa a thumb da 1ª aula |
| `nivel` | string(20) | `basico` · `intermediario` · `avancado` |
| `situacao` | string(20) | `rascunho` · `publicado` · `arquivado` |
| `moderar_comentarios` | bool | se true, pergunta nasce `pendente` |
| `liberacao_sequencial` | bool | **sempre false na v1** (D-06) |
| `total_aulas` | int unsigned | cache |
| `minutos_estimados` | int unsigned | cache, soma das durações |
| `posicao` | int | ordem manual na vitrine |
| `publicado_em` | timestamp null | |

`arquivado` **não** é exclusão: some do catálogo, mas quem já está matriculado
continua assistindo. Nunca `DELETE` em curso com matrícula.

### `modulos`

`curso_id` FK cascade · `titulo` · `descricao` null · `posicao` int.
Sem `slug`: módulo não tem URL própria.

### `aulas`

| Coluna | Tipo | Nota |
|---|---|---|
| `modulo_id` | FK cascade | |
| `titulo` · `slug` | | unique dentro do módulo |
| `descricao` | longtext null | |
| `provedor` | string(20) | `youtube` na v1; genérico por decisão D-02 |
| `video_id` | string(64) null | ID de 11 chars no YouTube |
| `duracao_segundos` | int unsigned | **denominador da regra dos 90%** |
| `amostra_gratuita` | bool | visível sem login na página do curso |
| `situacao` | string(20) | `rascunho` · `publicada` |
| `posicao` | int | |
| `publicada_em` | timestamp null | |

`duracao_segundos` não é cosmético. Se vier zero, a conclusão automática não
funciona. Ver `docs/05-integracao-youtube.md`.

### `materiais`

Anexos da aula. `aula_id` FK cascade · `titulo` · `disco` (default `materiais`) ·
`caminho` · `mime` null · `tamanho_bytes` · `total_downloads` · `posicao`.

O disco `materiais` é **privado**. Nada em `storage/app/public`.

### `matriculas`

| Coluna | Nota |
|---|---|
| `usuario_id` · `curso_id` | **unique juntos** |
| `origem` | `aluno` · `admin` · `importacao` |
| `situacao` | `ativa` · `concluida` · `cancelada` |
| `percentual_progresso` | tinyint 0–100, **cache** (D-05) |
| `ultima_aula_id` | FK null, `ON DELETE SET NULL` — "continue de onde parou" |
| `matriculado_em` · `concluido_em` null | |

`concluido_em` é timestamp, não booleano: publicar aula nova num curso já
concluído reabre a matrícula, e o histórico da conclusão anterior importa.

### `progresso_aulas`

Fonte de verdade do progresso. `matricula_id` + `aula_id` **unique juntos**.

| Coluna | Nota |
|---|---|
| `segundos_assistidos` | soma dos intervalos válidos |
| `posicao_maxima` | ponto mais avançado alcançado; **nunca diminui** |
| `primeira_visualizacao_em` | |
| `concluido_em` | null enquanto não concluída |

Pendura em `matricula_id`, não em `usuario_id`. Cancelar e refazer matrícula
vira operação limpa.

### `comentarios`

Perguntas e respostas dentro da aula. Auto-relação de um nível só.

`aula_id` · `usuario_id` · `comentario_pai_id` null · `corpo` text ·
`situacao` (`pendente`·`aprovado`·`oculto`) · `e_resposta` bool ·
`fixado` bool · softDeletes.

`e_resposta` marca resposta oficial do instrutor e sobe ao topo da thread.
Thread é rasa de propósito: pergunta → respostas. Sem resposta de resposta.

### `configuracoes`

Chave/valor para o que o admin muda sem deploy.
`chave` unique · `valor` text · `tipo` (`bool`·`int`·`string`·`json`).

Chaves da v1: `aprovacao_manual`, `percentual_conclusao`, `intervalo_ping`,
`texto_boas_vindas`.

---

## Índices que importam

```sql
matriculas         UNIQUE (usuario_id, curso_id)
progresso_aulas    UNIQUE (matricula_id, aula_id)
aulas              UNIQUE (modulo_id, slug)
cursos             INDEX  (situacao, publicado_em)
comentarios        INDEX  (aula_id, situacao, created_at)
matriculas         INDEX  (curso_id, situacao)
progresso_aulas    INDEX  (aula_id)
```

Os dois `UNIQUE` são de correção, não de desempenho: sem eles, dois cliques
rápidos em "Inscrever-me" criam matrícula dupla e o progresso se divide em duas
linhas.

---

## Tabelas do backlog (sem migration na v1)

Desenhadas para que o modelo atual não as impeça. Ver
`etapas/99-backlog-pos-mvp.md`.

- `questionarios` · `questoes` · `alternativas` · `tentativas` · `respostas`
- `certificados` (`matricula_id`, `codigo` unique, `emitido_em`, `pdf_caminho`)
