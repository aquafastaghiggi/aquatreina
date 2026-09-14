# Etapa 2 — Vitrine, catálogo e matrícula

> Objetivo: o aluno descobre um curso e se inscreve. Ainda não assiste.

**Pré-requisitos:** etapa 1 aprovada, com pelo menos um curso publicado.
**Duração estimada:** 1–1,5 semana.
**Leia antes:** `docs/03-mapa-de-telas-e-rotas.md`, `docs/07-design-e-ui.md`,
`padroes/03-livewire-e-filament.md`.

**Decisões que precisam estar respondidas:** A-02 (aprovação de conta),
A-05 (vínculo com organização), A-06 (marca).

---

## Tarefas

### 2.1 Matrícula

- [ ] Migration `matriculas` com o `UNIQUE (usuario_id, curso_id)`
- [ ] Model `Matricula`, enums `SituacaoMatricula` e `OrigemMatricula`
- [ ] `MatriculaFactory`
- [ ] Ação `MatricularAluno` — RN-05, idempotente, reativa matrícula cancelada
      preservando progresso
- [ ] Ação `CancelarMatricula` (uso do admin)
- [ ] `MatriculaPolicy`
- [ ] Evento `AlunoMatriculado` + e-mail de boas-vindas ao curso (na fila)

### 2.2 Vitrine pública

- [ ] `Publico\Vitrine` em `/`: proposta de valor em uma frase, prateleira de
      cursos publicados agrupados por categoria
- [ ] `Publico\PaginaCurso` em `/cursos/{slug}`: ementa com módulos e aulas
      listados sem link, duração total, nível, instrutor, botão de inscrição
      (ou de cadastro, se visitante)
- [ ] `/cursos/{slug}/amostra/{aula}`: toca a aula `amostra_gratuita` sem login
- [ ] Meta tags e Open Graph na página do curso
- [ ] Curso `arquivado` ou `rascunho` retorna 404 aqui

### 2.3 Catálogo do aluno

- [ ] `Aluno\Catalogo` em `/app/catalogo`
- [ ] Busca por título e subtítulo, com debounce
- [ ] Filtro por categoria e por nível
- [ ] Card indica quando já está inscrito
- [ ] Paginação
- [ ] Estado vazio com filtro: mensagem + botão de limpar

### 2.4 Painel do aluno

- [ ] `Aluno\Painel` em `/app`
- [ ] Bloco "Continue de onde parou" (por ora, o curso com matrícula mais recente)
- [ ] Cursos em andamento com barra de progresso (0% nesta etapa)
- [ ] Cursos concluídos, recolhidos
- [ ] Sugestões do catálogo
- [ ] Estado vazio: "Você ainda não está em nenhum treinamento" + botão

### 2.5 Inscrição

- [ ] `POST /app/catalogo/{curso}/inscrever`
- [ ] Só curso `publicado`; caso contrário, 404
- [ ] Idempotente: se já inscrito, redireciona sem erro
- [ ] Redireciona para `/app/c/{slug}` (que na etapa 3 leva à aula)

### 2.6 Perfil

- [ ] `Aluno\Perfil` em `/app/perfil`
- [ ] Editar nome, telefone, empresa, cargo, foto
- [ ] Trocar senha
- [ ] Bloco de dados pessoais guardados (LGPD, direito de acesso)

### 2.7 Admin

- [ ] Aba de matrículas na ficha do aluno em `UsuarioResource`
- [ ] Ação de matricular à força (`origem = admin`)
- [ ] Ação de cancelar matrícula

---

## Testes obrigatórios

- [ ] inscrição cria uma única matrícula mesmo com dois cliques (RN-05)
- [ ] inscrição em curso não publicado retorna 404 (RN-05)
- [ ] matrícula cancelada reativada preserva o progresso anterior (RN-05)
- [ ] catálogo lista só cursos publicados
- [ ] busca do catálogo filtra por título
- [ ] página pública de curso arquivado retorna 404
- [ ] aula de amostra abre sem login; aula comum não
- [ ] visitante clicando em inscrever vai para o cadastro

---

## Critérios de aceite

1. Visitante encontra um curso pela vitrine e vê a ementa completa.
2. A aula de amostra toca sem login.
3. Aluno logado se inscreve em um clique e o curso aparece em `/app`.
4. Clicar duas vezes rápido não cria matrícula duplicada.
5. Busca e filtro do catálogo funcionam, com estado vazio tratado.
6. Tudo utilizável a 400px de largura.
7. Pint e testes verdes.

## Não faça nesta etapa

Player, progresso, conclusão, comentários, materiais para o aluno.
