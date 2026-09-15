# 04 — Regras de negócio

Cada regra tem um identificador. Testes devem citá-lo (ver `padroes/05-testes.md`).

---

## RN-01 · Conclusão de aula

Uma aula é concluída quando **qualquer** das condições ocorre:

- **Automática:** `posicao_maxima >= duracao_segundos * (percentual_conclusao / 100)`,
  com `percentual_conclusao` = 90 por padrão.
- **Manual:** o aluno clica em "Marcar como concluída".

Ao concluir: grava `progresso_aulas.concluido_em = now()`, dispara
`AulaConcluida`, recalcula `matriculas.percentual_progresso` e atualiza
`matriculas.ultima_aula_id`.

Concluir é **idempotente**. Segunda chamada não altera `concluido_em`.

Se `aulas.duracao_segundos` for 0, a via automática não funciona. A interface
deve continuar oferecendo a via manual, e o admin deve ver um aviso na aula.

## RN-02 · Validação do progresso

O cliente envia `{aula_id, posicao}` a cada `intervalo_ping` segundos (10).
O servidor:

1. rejeita se não houver matrícula ativa no curso da aula → 403;
2. rejeita `posicao` negativa ou maior que `duracao_segundos + 5` → 422;
3. calcula `delta = posicao - posicao_maxima`;
4. se `delta <= 0`, **ignora** (o aluno voltou no vídeo; isso não é erro);
5. se `delta > intervalo_ping * 2.5`, considera salto de barra: atualiza
   `posicao_maxima` mas **não soma** em `segundos_assistidos`;
6. caso contrário, soma `delta` em `segundos_assistidos` e atualiza `posicao_maxima`.

`posicao_maxima` nunca diminui. Arrastar a barra até o fim **não** conclui a
aula, porque `segundos_assistidos` não acompanha.

## RN-03 · Navegação dentro do curso

Todas as aulas com `situacao = publicada` do curso matriculado estão acessíveis,
em qualquer ordem. `cursos.liberacao_sequencial` existe mas é `false` na v1 e
não tem tela.

Aula em `rascunho` retorna 404 para o aluno, inclusive por URL direta.

## RN-04 · Conclusão de curso

A matrícula passa a `concluida` quando **100% das aulas publicadas** do curso
estão concluídas. Grava `concluido_em`.

Publicar aula nova em curso já concluído: a matrícula volta para `ativa`,
`percentual_progresso` cai, `concluido_em` é preservado no histórico e o aluno
recebe notificação de conteúdo novo.

Despublicar aula: o denominador diminui e o percentual sobe. O recálculo trata
os dois sentidos.

## RN-05 · Inscrição

- Um clique em `POST /app/catalogo/{curso}/inscrever`.
- Só em curso `publicado`.
- **Idempotente**: se já existe matrícula, redireciona para o curso sem erro.
- Matrícula `cancelada` que é refeita vira `ativa` de novo, **preservando** o
  progresso anterior.
- Cria com `origem = aluno`. Admin cria com `admin`; importação com `importacao`.

## RN-06 · Perguntas

- Só aluno com matrícula ativa comenta na aula.
- Nasce `aprovado`, salvo se `cursos.moderar_comentarios` for true — aí nasce
  `pendente` e só o autor e a moderação enxergam.
- Resposta do instrutor (`e_resposta = true`) sobe ao topo da thread e dispara
  notificação + e-mail ao autor da pergunta.
- `fixado` prende a thread no topo da aula. Usado para pergunta frequente.
- Exclusão é sempre lógica (`softDeletes`). Admin oculta com `situacao = oculto`.
- Limite: 10 comentários por minuto por usuário.

## RN-07 · Materiais

- Download só por `GET /app/materiais/{material}/baixar`, que valida matrícula
  ativa no curso da aula do material.
- Incrementa `total_downloads` e devolve stream. Arquivo nunca em `public/`.
- Aula sem material esconde a aba inteira.

## RN-08 · Situação da conta

| Situação | Pode |
|---|---|
| `pendente` | logar e ver `/aguardando-aprovacao`. Nada mais. |
| `ativo` | tudo que o papel permite |
| `bloqueado` | nada. Logout forçado na próxima requisição. |

Conta nova sempre entra como `pendente`. Somente o administrador pode mudar a
situacao para `ativo`; não há verificação de e-mail no cadastro.

## RN-09 · Publicação de curso

Para publicar, o curso precisa de: título, categoria, capa (própria ou herdada
da primeira aula), pelo menos **um módulo com uma aula publicada**, e toda aula
publicada com `video_id` preenchido.

A validação roda na ação de publicar e lista tudo que falta de uma vez — não um
erro por vez.

## RN-10 · Arquivamento

Curso `arquivado` sai do catálogo e da vitrine. Matrículas existentes continuam
funcionando, incluindo materiais e perguntas. Não há exclusão de curso com
matrícula.

## RN-11 · Último acesso

`usuarios.ultimo_acesso_em` atualiza no máximo uma vez por hora, via middleware.
Escrever a cada requisição transforma toda navegação em `UPDATE`.

---

## Constantes

Definidas em `config/treina.php`, lidas do `.env`:

| Constante | Padrão | Onde é usada |
|---|---|---|
| `percentual_conclusao` | 90 | RN-01 |
| `intervalo_ping` | 10 | RN-02 |
| `tolerancia_salto` | `intervalo_ping * 2.5` | RN-02 |
| `limite_comentarios_minuto` | 10 | RN-06 |

Nenhum desses números deve aparecer solto no código.
