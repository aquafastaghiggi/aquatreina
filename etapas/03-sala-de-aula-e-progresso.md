# Etapa 3 — Sala de aula e progresso

> Objetivo: o aluno assiste, o progresso é registrado com confiança e o curso
> pode ser concluído. É o coração do produto.

**Pré-requisitos:** etapa 2 aprovada.
**Duração estimada:** 1,5–2 semanas.
**Leia antes:** `docs/04-regras-de-negocio.md` (RN-01 a RN-04),
`docs/05-integracao-youtube.md` inteiro, `padroes/03-livewire-e-filament.md`.

**Decisão que precisa estar respondida:** A-01 (existe conteúdo confidencial?).

---

## Tarefas

### 3.1 Progresso

- [ ] Migration `progresso_aulas` com o `UNIQUE (matricula_id, aula_id)`
- [ ] Model `ProgressoAula` e factory
- [ ] Ação `RegistrarProgresso` — **RN-02 exatamente como escrita**, cinco
      cenários tratados
- [ ] Ação `ConcluirAula` — RN-01, idempotente, dispara `AulaConcluida`
- [ ] Ação `RecalcularProgresso` — atualiza `matriculas.percentual_progresso` e
      `ultima_aula_id`
- [ ] Ação `VerificarConclusaoDoCurso` — RN-04, nos dois sentidos
- [ ] Eventos `AulaConcluida` e `CursoConcluido` com listeners síncronos para
      cache e em fila para e-mail
- [ ] Job `ConferirProgressoDiario` reconciliando o cache com `progresso_aulas`

### 3.2 Sala de aula

- [ ] `Aluno\SalaDeAula` em `/app/c/{curso:slug}/a/{aula:slug}`
- [ ] `/app/c/{curso:slug}` redireciona para a próxima aula não concluída
- [ ] Autorização no `mount()`: matrícula ativa no curso **da aula**, via
      `aula → modulo → curso` (não confie no slug da URL)
- [ ] Aula em rascunho retorna 404 mesmo por URL direta (RN-03)
- [ ] Player embed conforme a tabela de parâmetros do doc de vídeo
- [ ] Botões "Marcar como concluída" e "Próxima aula"
- [ ] Abas Sobre a aula · Materiais · Perguntas (as duas últimas só estruturadas;
      conteúdo vem na etapa 4). Aba sem conteúdo fica oculta.
- [ ] Índice lateral: acordeão por módulo, módulo atual aberto, ícone de estado,
      duração alinhada com `tabular-nums`
- [ ] Bloco de progresso: percentual, barra e "N de M aulas"
- [ ] No celular: player primeiro, índice colapsado **abaixo**

### 3.3 Rastreamento

- [ ] `resources/js/player-aula.js` em JS puro, fora do Livewire
- [ ] IFrame API, ping a cada `config('treina.intervalo_ping')` enquanto tocando
- [ ] Ping final em `PAUSED`, `ENDED`, `beforeunload` e `visibilitychange`
- [ ] `navigator.sendBeacon` na saída
- [ ] `POST /app/progresso` com `throttle:120,1`
- [ ] Resposta JSON com `posicao_maxima`, `segundos_assistidos`, `concluida`,
      `percentual_curso`
- [ ] Ao concluir: índice e barra atualizam sem recarregar a página
- [ ] Retomar a aula oferece continuar do ponto salvo

### 3.4 Painel

- [ ] "Continue de onde parou" usando `ultima_aula_id` de verdade
- [ ] Barras de progresso reais
- [ ] Curso concluído muda de seção automaticamente

### 3.5 Admin

- [ ] Coluna de progresso na ficha do aluno
- [ ] Widget de conclusão por curso
- [ ] Widget de aulas com maior abandono

---

## Testes obrigatórios

RN-02, os cinco cenários:

- [ ] avanço normal soma tempo e atualiza posição
- [ ] voltar no vídeo não diminui `posicao_maxima` e não soma
- [ ] salto de barra atualiza posição mas **não** soma tempo
- [ ] posição maior que a duração retorna 422
- [ ] ping sem matrícula ativa retorna 403

RN-01 e RN-04:

- [ ] atingir 90% conclui automaticamente
- [ ] botão manual conclui
- [ ] concluir duas vezes não altera `concluido_em`
- [ ] aula com `duracao_segundos = 0` não conclui sozinha, mas conclui no manual
- [ ] concluir a última aula marca a matrícula como `concluida`
- [ ] publicar aula nova reabre a matrícula concluída
- [ ] despublicar aula recalcula o percentual para cima

RN-03:

- [ ] aula em rascunho retorna 404 por URL direta
- [ ] aula de outro curso, sem matrícula, retorna 403

---

## Critérios de aceite

1. O aluno assiste uma aula inteira e ela é marcada como concluída sozinha.
2. Arrastar a barra até o fim **não** conclui a aula.
3. Fechar a aba no meio e voltar depois retoma do ponto certo.
4. Concluir a última aula conclui o curso e isso aparece em `/app`.
5. A sala de aula é utilizável num celular de 400px.
6. Nenhum ping de progresso passa por Livewire.
7. Pint e testes verdes.

## Não faça nesta etapa

Comentários, download de material, notificações, relatórios.
