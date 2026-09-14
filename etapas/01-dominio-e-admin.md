# Etapa 1 — Domínio e administração de conteúdo

> Objetivo: o admin consegue montar um curso completo, com módulos, aulas
> ligadas a vídeos do YouTube e materiais. O aluno ainda não vê nada.

**Pré-requisitos:** etapa 0 aprovada.
**Duração estimada:** 2–3 semanas. É a maior etapa.
**Leia antes:** `docs/02-modelo-de-dados.md`, `docs/05-integracao-youtube.md`,
`padroes/03-livewire-e-filament.md`, `padroes/04-banco-e-migrations.md`.

---

## Tarefas

### 1.1 Migrations e models

Na ordem de `artefatos/migrations.md`:

- [ ] `categorias`, `cursos`, `modulos`, `aulas`, `materiais`, `configuracoes`
- [ ] Models com `$fillable`, `casts()`, relacionamentos tipados, `SoftDeletes`
      onde previsto
- [ ] Enums: `SituacaoCurso`, `SituacaoAula`, `NivelCurso`, `ProvedorVideo`
- [ ] Scopes `Curso::publicados()`, `Aula::publicadas()`
- [ ] Factories de tudo
- [ ] `CategoriaSeeder`

Confira contra `artefatos/schema.sql`. Nome de coluna e valor de enum precisam
bater exatamente.

### 1.2 Serviço de vídeo

- [ ] `app/Servicos/Video/` com a interface `ProvedorVideo` e `YoutubeProvedor`
- [ ] `ExtratorIdVideo` cobrindo todos os formatos de `docs/05-integracao-youtube.md`
- [ ] `LeitorMetadadosVideo`: Data API v3 → fallback oEmbed → falha graciosa
- [ ] Cache de 24h dos metadados
- [ ] `urlEmbed()` com todos os parâmetros da tabela do doc
- [ ] `urlThumb()` com queda de `maxresdefault` para `hqdefault`
- [ ] Nenhuma menção a "youtube" fora dessa pasta

### 1.3 Policies

- [ ] `CursoPolicy`: instrutor só edita curso onde é `responsavel_id`; admin edita tudo
- [ ] `AulaPolicy`, `MaterialPolicy` derivando do curso
- [ ] Filament usando essas Policies

### 1.4 Ações de curso

Em `app/Acoes/Curso/`:

- [ ] `PublicarCurso` — valida RN-09 e devolve **todas** as pendências de uma vez
- [ ] `ArquivarCurso` — RN-10
- [ ] `ReordenarCurriculo` — recebe a nova ordem de módulos e aulas, salva numa transação
- [ ] `SalvarAula` — usa o serviço de vídeo para preencher título e duração
- [ ] `RecalcularCachesDoCurso` — `total_aulas` e `minutos_estimados`

### 1.5 Filament

- [ ] `CategoriaResource` com ordenação
- [ ] `OrganizacaoResource`
- [ ] `CursoResource` com as abas Dados · Conteúdo · Publicação
- [ ] Ação **Publicar** exibindo a lista de pendências quando falha
- [ ] Upload de capa

### 1.6 Construtor de currículo

`app/Filament/Pages/ConstrutorCurriculo.php`, rota `/admin/cursos/{registro}/curriculo`.

- [ ] Coluna esquerda: módulos e aulas, arrastar e soltar, salvando `posicao`
      numa chamada ao soltar
- [ ] Criar, renomear e excluir módulo
- [ ] Criar, duplicar e excluir aula
- [ ] Coluna direita: edição da aula — título, descrição, link do vídeo,
      amostra gratuita, situação
- [ ] Colar link do YouTube preenche título e duração automaticamente, com
      indicação visível de sucesso ou de queda para o modo manual
- [ ] Aviso quando `duracao_segundos` for 0: a conclusão automática não vai funcionar
- [ ] Materiais da aula: upload, renomear, reordenar, excluir
- [ ] Upload no disco privado `materiais`, validando MIME real e 20 MB

### 1.7 Eventos

- [ ] `AulaPublicada`, `CursoPublicado`
- [ ] Listener recalculando os caches do curso

---

## Testes obrigatórios

- [ ] `ExtratorIdVideo` com tabela de casos: 6 formatos válidos + 4 inválidos
- [ ] leitor de metadados converte `PT12M15S` em 735 segundos
- [ ] leitor cai para oEmbed quando a chave da API está ausente (`Http::fake()`)
- [ ] aula é salva mesmo com as duas fontes de metadados indisponíveis
- [ ] instrutor não edita curso de outro instrutor
- [ ] publicar curso sem aula publicada falha e lista as pendências (RN-09)
- [ ] reordenar currículo persiste `posicao` de módulos e aulas
- [ ] upload rejeita arquivo acima de 20 MB e MIME não permitido

---

## Critérios de aceite

1. Admin cria curso, módulos e aulas, e monta um currículo completo pela interface.
2. Colar `https://youtu.be/<id>` preenche título e duração sem digitação.
3. Reordenar por arrastar e soltar persiste após recarregar a página.
4. Publicar curso incompleto mostra tudo que falta de uma vez.
5. Materiais sobem para disco privado e não são acessíveis por URL direta.
6. Instrutor logado só vê e edita os cursos dele.
7. Pint e testes verdes.

## Não faça nesta etapa

Catálogo, inscrição, sala de aula, progresso, comentários. O aluno ainda não
entra em cena.
