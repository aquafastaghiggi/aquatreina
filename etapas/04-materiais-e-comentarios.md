# Etapa 4 — Materiais e perguntas

> Objetivo: o aluno baixa material e pergunta; o instrutor responde e o aluno é
> avisado.

**Pré-requisitos:** etapa 3 aprovada.
**Duração estimada:** 1–1,5 semana.
**Leia antes:** RN-06 e RN-07 em `docs/04-regras-de-negocio.md`,
`docs/06-seguranca-e-lgpd.md` §3.

---

## Tarefas

### 4.1 Materiais para o aluno

- [ ] Aba **Materiais** na sala de aula, listando título, tipo e tamanho
- [ ] `GET /app/materiais/{material}/baixar` validando matrícula ativa no curso
      da aula do material (RN-07)
- [ ] Incrementa `total_downloads` e devolve `Storage::download()` com o nome
      bonito de `materiais.titulo`
- [ ] Aula sem material esconde a aba inteira
- [ ] `MaterialPolicy` cobrindo o acesso
- [ ] Contador de downloads visível no admin

### 4.2 Perguntas

- [ ] Migration `comentarios` com `comentario_pai_id` e `softDeletes`
- [ ] Model `Comentario`, enum `SituacaoComentario`, factory
- [ ] Scope `visiveis()`
- [ ] Ação `CriarComentario` — RN-06, respeitando `cursos.moderar_comentarios`
- [ ] Ação `ResponderComentario` — marca `e_resposta`, dispara evento
- [ ] Ação `ModerarComentario` — aprovar, ocultar, fixar
- [ ] `ComentarioPolicy`
- [ ] `throttle` de 10 por minuto por usuário

### 4.3 Interface das perguntas

- [ ] `Aluno\AbaComentarios` como componente filho da sala de aula
- [ ] Lista: fixados primeiro, depois mais recentes; resposta do instrutor no
      topo da thread
- [ ] Campo de nova pergunta, com estado vazio convidativo
- [ ] Pergunta `pendente` visível só para o autor, com aviso de "em análise"
- [ ] Contador na aba
- [ ] Paginação a partir de 10 threads

### 4.4 Moderação no admin

- [ ] `ComentarioResource`: fila única de todos os cursos
- [ ] Filtros: sem resposta, pendentes, por curso
- [ ] Responder sem sair da tela
- [ ] Ocultar, fixar, restaurar
- [ ] Instrutor vê só os cursos dele

### 4.5 Notificações

- [ ] `ComentarioRespondido` → notificação no app + e-mail ao autor
- [ ] `AulaPublicada` em curso com matrícula → notificação aos matriculados
- [ ] `/app/notificacoes` espelhando o e-mail, com marcar como lida
- [ ] Sino com contador no cabeçalho do aluno
- [ ] Todo envio de e-mail na fila `emails`

### 4.6 Widget do painel admin

- [ ] **Perguntas sem resposta** como primeiro widget, com link para a fila

---

## Testes obrigatórios

- [ ] download sem matrícula ativa retorna 403 (RN-07)
- [ ] download incrementa `total_downloads`
- [ ] material não é acessível por URL direta no storage
- [ ] comentário nasce `aprovado` em curso normal (RN-06)
- [ ] comentário nasce `pendente` quando `moderar_comentarios` é true (RN-06)
- [ ] comentário `pendente` é invisível para outro aluno e visível para o autor
- [ ] aluno sem matrícula não comenta
- [ ] `throttle` corta o 11º comentário no mesmo minuto
- [ ] resposta do instrutor notifica o autor da pergunta
- [ ] instrutor não modera comentário de curso alheio

---

## Critérios de aceite

1. O aluno baixa um PDF da aula; sem matrícula, recebe 403.
2. A URL direta do arquivo no storage não funciona.
3. O aluno pergunta, o instrutor responde pelo admin, e o aluno recebe
   notificação e e-mail.
4. Em curso com moderação, a pergunta só aparece depois de aprovada.
5. O painel admin mostra a fila de perguntas sem resposta em primeiro lugar.
6. Pint e testes verdes.

## Não faça nesta etapa

Relatórios, exportação, importação de alunos, anonimização.
