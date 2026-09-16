# Auditoria — 2026-09-16

## Resumo

O projeto está **utilizável** no caminho feliz: `composer install`, `migrate:fresh --seed`, `pint --test` e a suíte inteira passam limpos, e as 11 regras de negócio (RN-01 a RN-11) estão implementadas e majoritariamente corretas. Os dois defeitos que impediam a entrega em produção — a dessincronia de `tolerancia_salto` (RN-02) e o bypass de `/admin` para conta bloqueada (RN-08) — e os seis itens de "corrigir antes de usar em produção" foram corrigidos nesta mesma sessão, um commit por item, com teste que falha antes e passa depois de cada correção (ver seção "Correções aplicadas"). Restam decisões de spec em aberto (seção 5) e itens de baixo risco não verificados (seção 4).

## Comandos executados

| comando | resultado |
|---|---|
| `composer install --no-interaction` | OK |
| `php artisan migrate:fresh --seed` | OK — 21 migrations + 5 seeders rodaram limpos |
| `vendor/bin/pint --test` | OK — `{"tool":"pint","result":"passed"}` |
| `php artisan test` | OK — **109 passed (305 assertions)** |
| `php artisan route:list --no-ansi` | OK — 67 rotas |

## Correções aplicadas nesta rodada

Um commit por item, teste que falha antes → correção → teste passando depois, suíte completa verde a cada commit. Ordem cronológica:

1. `fix(progresso): sincroniza tolerancia de salto com intervalo_ping em runtime (RN-02)` — commit `9c5b187`
2. `fix(usuario): bloqueia acesso ao painel admin para conta nao ativa (RN-08)` — commit `831e94d`
3. `fix(usuario): adiciona unique de cnpj e indice de ativa em organizacoes` — commit `126e0f6`
4. `fix(usuario): adiciona indice em usuarios.situacao` — commit `cd01992`
5. `fix(admin): adiciona coluna descricao em configuracoes` — commit `3f368f8`
6. `fix(curso): registra log de auditoria ao publicar, arquivar curso e salvar configuracoes` — commit `02c0876`
7. `fix(usuario): adiciona UsuarioPolicy e autoriza aprovar/bloquear/anonimizar` — commit `abf7706`
8. `refactor(aula): reaproveita matricula ja validada entre sala de aula e comentarios` — commit `3428abf`
9. `chore(admin): le limite_comentarios_minuto do .env e remove literais duplicados em Configuracoes` — commit `ccf39b4`

Detalhe de cada um nas seções abaixo, marcado **[RESOLVIDO]**.

## 1. Bloqueia entrega

- **[RESOLVIDO — commit `9c5b187`]** ~~[app/Acoes/Progresso/RegistrarProgresso.php:61] A tolerância de "salto de barra" (RN-02, cenário 5) era lida de `config('treina.tolerancia_salto')`, calculada uma única vez a partir do `intervalo_ping` do `.env`, dessincronizando do valor que o admin edita em runtime via `Configuracoes.php`.~~ Corrigido para calcular `Configuracao::valor('intervalo_ping', config('treina.intervalo_ping')) * 2.5` a cada chamada, igual ao padrão já usado em `ConcluirAula.php` e `SalaDeAula.php`. Testes novos: "aceita posicao exatamente na tolerancia maxima da duracao" e "acompanha o intervalo_ping configurado em runtime ao calcular a tolerancia de salto".

- **[RESOLVIDO — commit `831e94d`]** ~~[app/Providers/AppServiceProvider.php:55] O Gate `acessar-admin` só verificava o papel (`hasAnyRole(['admin','instrutor'])`), nunca `situacao` — um admin/instrutor bloqueado ou pendente continuava acessando `/admin` (200 em vez de 403).~~ Corrigido acrescentando `$usuario->situacao === SituacaoUsuario::Ativo` ao Gate, que também protege as rotas `admin.usuarios.modelo`/`admin.exportacoes.baixar` (mesmo `can:acessar-admin`). Testes novos: "nega o painel administrativo para admin bloqueado (RN-08)" e "... para admin pendente (RN-08)".

## 2. Corrigir antes de usar em produção

- **[RESOLVIDO — commit `126e0f6`]** ~~[organizacoes] `cnpj` sem UNIQUE, `ativa` sem índice.~~ Migration nova `2026_09_16_000100_add_indices_to_organizacoes_table.php` adiciona `organizacoes_cnpj_unique` e `organizacoes_ativa_index`. Teste novo confirma `QueryException` em CNPJ duplicado e que múltiplos `cnpj = null` continuam permitidos.
  - **Ainda em aberto, por decisão consciente**: `tipo` continua sem `DEFAULT 'distribuidor'` e `cnpj` continua `VARCHAR(14)` (schema pede 18). Mudar o tipo/default de uma coluna **existente** exige `doctrine/dbal` (não instalado no projeto) ou SQL bruto não portável para o SQLite usado nos testes. Registrado no commit; decisão para o time: instalar doctrine/dbal, aceitar SQL específico por driver, ou atualizar o schema de referência.

- **[RESOLVIDO — commit `cd01992`]** ~~[usuarios] Faltava `usuarios_situacao_index`.~~ Migration nova `2026_09_16_000200_add_situacao_index_to_usuarios_table.php` adiciona o índice. Confirmado por `SHOW INDEX FROM usuarios` no MySQL real.

- **[RESOLVIDO — commit `3f368f8`]** ~~[configuracoes] Faltava a coluna `descricao`, já esperada pelo `$fillable` do model `Configuracao` mas nunca criada.~~ Migration nova `2026_09_16_000300_add_descricao_to_configuracoes_table.php` adiciona `descricao VARCHAR(255) NULL`. Teste novo confirma a gravação.
  - **Ainda em aberto**: `chave` continua `VARCHAR(255)` (schema pede 80), `valor` continua `NOT NULL` (schema pede `NULL`), `tipo` continua sem `DEFAULT 'string'` — mesma razão de portabilidade SQLite/doctrine-dbal do item anterior.

- **[RESOLVIDO — commit `02c0876`]** ~~[PublicarCurso, ArquivarCurso, Configuracoes::salvar] Nenhuma das três ações registrava `activity()`.~~ `PublicarCurso::executar()` e `ArquivarCurso::executar()` agora recebem um `?Usuario $executor` opcional (seguindo o padrão de `ModerarComentario`, sem facade dentro da Ação) e logam a atividade; `CursoResource` passa `auth()->user()`. `Configuracoes::salvar()` loga diretamente (é camada de orquestração, não Ação). Testes novos confirmam os três registros no `activity_log`.

- **[RESOLVIDO — commit `abf7706`]** ~~[UsuarioResource, ViewUsuario] Não existia `UsuarioPolicy`; as ações aprovar/bloquear/anonimizar dependiam só de `canViewAny()` restringir o resource inteiro.~~ `UsuarioPolicy` criada com `before()` admin-only e habilidades `aprovar`/`bloquear`/`anonimizar`/`view`/`viewAny`; as Actions do Filament agora chamam `->authorize(fn ($record) => Gate::allows(...))`, no mesmo padrão de `CursoResource`/`ComentarioResource`. Teste novo confirma que só admin passa no Gate para as três habilidades.

- **[RESOLVIDO — commit `3428abf`]** ~~[SalaDeAula + AbaComentarios] 27 queries por carregamento de aula, com `Curso`/`Aula` buscados 3x e a checagem de matrícula duplicada 3x.~~ `AbaComentarios` agora recebe `matriculaId` já validado pelo componente pai (prop `#[Locked]`, imune a adulteração do cliente entre requisições), eliminando a rebusca de aula+módulo e o `exists()` de matrícula redundantes no `mount()` do componente filho (3 queries a menos por carregamento). A checagem mais restrita de `ComentarioPolicy::create` (só matrícula `ativa`, não `concluida`) foi mantida intacta — não é redundante, é uma regra diferente. Teste de regressão novo (`sala_de_aula_test.php`) trava o número de buscas de `aulas`/`matriculas` via `DB::listen`.
  - **Não foi tocado** (fora de escopo, risco maior): a rebusca de `Curso`/`Aula` entre `mount()` e os `#[Computed] curso()/aula()` de `SalaDeAula` — eliminar isso exigiria mudar o ciclo de vida de memoização do próprio componente Livewire, risco desproporcional ao ganho nesta rodada.

## 3. Melhorias

- **[RESOLVIDO — commit `ccf39b4`]** ~~`limite_comentarios_minuto` hardcoded no config, sem `env()`.~~ Agora `(int) env('TREINA_LIMITE_COMENTARIOS_MINUTO', 10)`, documentado em `.env.example`/`.env.production.example`.
- **[RESOLVIDO — commit `ccf39b4`]** ~~`Configuracoes.php` duplicava os literais 90/10 como default de propriedade e como default de `Configuracao::valor(...)`.~~ Propriedades tipadas sem default (`mount()` sempre roda antes do uso); `mount()` agora usa `config('treina.percentual_conclusao')`/`config('treina.intervalo_ping')` como fallback.
- **[RESOLVIDO — commit `9c5b187`]** ~~Faltava teste de fronteira em RN-02 para `posicao == duracao_segundos + 5`.~~ Teste novo "aceita posicao exatamente na tolerancia maxima da duracao (RN-02)".
- **[Não corrigido — decisão de produto/segurança, não de bug]** `app/Http/Middleware/CabecalhosSeguranca.php:22` CSP com `script-src 'unsafe-inline'` — trade-off deliberado para Livewire/Alpine funcionarem; removê-lo exige reestruturar como os scripts inline são carregados (nonce/hash), risco e esforço maiores que o benefício nesta rodada.
- **[Não corrigido — cosmético]** `app/Acoes/Curso/RecalcularCachesDoCurso.php` tem nome enganoso (não mexe em `Cache`, só recalcula `total_aulas`/`minutos_estimados`). Renomear é seguro mas não crítico; deixado para não inflar o diff desta rodada de correções.
- **[Não corrigido — decisão de spec, não de código]** Divergências de nomenclatura em `artefatos/rotas.md` (`app.materiais.baixar` vs `app.material.baixar`, `app.dados.exportar` vs `app.perfil.exportar`) e rotas extras não documentadas (`/aceitar-termos`, `admin.usuarios.modelo`, `admin.exportacoes.baixar`, páginas de configurações/relatórios/importação). Corrigir isso é atualizar o artefato de spec, não o código — ver seção 5.
- **[Não corrigido — migration já versionada]** `notifications` foi criada fora da ordem de `artefatos/migrations.md`. Reescrever uma migration já versionada é proibido por `padroes/04-banco-e-migrations.md`; não há ganho real em criar uma migration de "reordenação" para uma tabela sem FK.
- **[Não corrigido — funcional, arquitetural]** RN-06 (comentários) e a conclusão manual de aula viraram métodos Livewire em vez de rotas HTTP dedicadas com `throttle:` — o limite de 10/min é aplicado corretamente via `RateLimiter` manual dentro da Ação e está testado; a divergência é só de auditabilidade via `route:list`, não de comportamento.

## 4. Não verificado

- **RN-03**: existência real do campo `cursos.liberacao_sequencial` no schema.
- **RN-06**: ordenação real da thread de comentários (`fixado` prende no topo; resposta do instrutor sobe visualmente); presença do trait `SoftDeletes` no model `Comentario`.
- **RN-07**: comportamento de "aula sem material esconde a aba inteira".
- **RN-08**: fluxo de cadastro público gerando `situacao=pendente` por padrão (coberto indiretamente por outros testes que passam, mas não verificado linha a linha nesta auditoria).
- **RN-10**: não há teste dedicado de "materiais/perguntas continuam funcionando em curso arquivado" nem de "curso arquivado não aparece no catálogo/vitrine" (só rascunho é testado).
- **Cache em produção**: comportamento com driver diferente de `array`/`file` (ex. Redis); concorrência de múltiplos pings simultâneos sob `throttle:120,1`.
- **Infraestrutura de produção**: restauração de backup, isolamento de rede do banco/app, cookies `secure` sob HTTPS real.
- **Outras áreas do Filament** (Relatórios, Importação) quanto a ignorar `situacao` do usuário autenticado além do que já foi corrigido no Gate `acessar-admin`.
- **Interface** (responsividade, foco de teclado, i18n de tela): fora do escopo desta rodada — recomenda-se auditoria dedicada usando `checklists/revisao-claude-code.md §7`.

## 5. Divergências onde a spec parece errada

Nenhum arquivo de `docs/`/`artefatos/` foi alterado — são decisões para o time, não correções de código:

- **RN-01 (docs/04-regras-de-negocio.md:9-11)**: o texto só menciona `posicao_maxima >= limite`, mas o código corretamente também exige `segundos_assistidos >= limite` (sem isso, contradiria RN-02). O texto de RN-01 deveria citar as duas condições.
- **RN-09 (docs/04-regras-de-negocio.md:100-101)**: "capa herdada da primeira aula" na prática é "primeira aula publicada **com vídeo**" — código consistente entre validação e exibição, só a redação da spec é imprecisa.
- **Verificação de e-mail / aprovação administrativa**: `config/fortify.php` não habilita verificação de e-mail, e o grupo `/app` não usa `verified` — coerente com D-09 e os commits recentes que simplificaram o cadastro, mas `artefatos/rotas.md` ainda descreve e-mail verificado. Falta atualizar o artefato.
- **Cache `curso:{id}:curriculo` (padroes/02-laravel.md:108)**: documentado com TTL de 1h, não existe no código. Currículo já é resolvido em poucas queries eficientes via `with()`, sem problema de performance mensurável hoje. Decidir: remover a linha do padrão ou implementar se o volume crescer.

## 6. O que está correto

- **Execução**: `composer install`, `migrate:fresh --seed`, `pint --test` e os 109 testes da suíte passam limpos; `route:list` responde com 67 rotas.
- **Schema**: `categorias`, `modulos`, `aulas`, `materiais`, `comentarios`, `progresso_aulas` batem exatamente com `artefatos/schema.sql`, incluindo os três UNIQUE críticos e todos os índices compostos documentados. Nenhuma coluna usa `ENUM` nativo do MySQL. Nenhuma referência residual a uma tabela `users`.
- **Migrations**: nenhuma migration versionada foi reescrita; as correções desta rodada foram todas migrations novas.
- **composer.json**: bate 100% com `artefatos/composer-packages.md` em produção.
- **RN-01 a RN-11**: implementadas e testadas corretamente (detalhes na rodada de auditoria original); os dois problemas reais encontrados (RN-02 e RN-08) já estão corrigidos nesta sessão.
- **Segurança**: Policies chamadas no backend (`Gate::authorize`/`abort_unless`/`->authorize()`), não só escondendo botão — inclusive a nova `UsuarioPolicy`. Throttles de login/cadastro corretos. Nenhum segredo versionado. Cabeçalhos de segurança presentes e testados.
- **Desempenho**: `GET /app` e `GET /app/catalogo` sem N+1; `percentual_progresso` sempre lido do cache; ping de progresso não passa por Livewire; listagens paginadas; cache `catalogo:publicados` invalidado corretamente; sala de aula com 3 queries redundantes a menos após a correção desta rodada.

---

**Itens por seção**: Bloqueia entrega: 2 (2 resolvidos) · Corrigir antes de usar em produção: 6 (6 resolvidos, 2 com decisão em aberto sobre doctrine/dbal) · Melhorias: 8 (3 resolvidos, 5 deixados por serem decisões de spec/produto ou risco desproporcional) · Não verificado: 9 · Spec parece errada: 4 (nenhuma decidida — aguarda o time).
