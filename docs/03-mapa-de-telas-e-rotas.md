# 03 — Mapa de telas e rotas

Nomes de rota em português, com ponto separando o grupo. O mapa completo e
copiável está em `artefatos/rotas.md`.

---

## Área pública — sem login

| Rota | Nome | Tela |
|---|---|---|
| `GET /` | `vitrine` | Proposta de valor em uma frase, prateleira de cursos publicados, CTA de cadastro. Única página que precisa vender. |
| `GET /cursos/{curso:slug}` | `cursos.mostrar` | Ementa com módulos e aulas listados (sem link), duração total, instrutor, botão de inscrição. Indexável. |
| `GET /cursos/{curso:slug}/amostra/{aula:slug}` | `cursos.amostra` | Toca a aula marcada como `amostra_gratuita`. Sem login. |
| `GET /entrar` · `POST /entrar` | `login` | Fortify |
| `GET /cadastrar` · `POST /cadastrar` | `cadastro` | nome, e-mail, telefone, empresa, cargo, aceite de termos |
| `GET /senha/esqueci` · `POST` | `senha.solicitar` | Fortify |
| `GET /senha/redefinir/{token}` · `POST` | `senha.redefinir` | Fortify |
| `GET /email/verificar` | `verification.notice` | nome do framework, mantido |
| `GET /email/verificar/{id}/{hash}` | `verification.verify` | assinada |
| `GET /termos` · `GET /privacidade` | `termos` · `privacidade` | páginas estáticas |

**Página de conta pendente.** Usuário com `situacao = pendente` que verificou o
e-mail cai em `GET /aguardando-aprovacao` (`conta.pendente`), não numa tela de
erro. Middleware `garantir.ativo`.

---

## Área do aluno — `/app`

Middleware: `auth` · `verified` · `garantir.ativo`.

| Rota | Nome | Tela |
|---|---|---|
| `GET /app` | `app.painel` | "Continue de onde parou" em destaque, cursos em andamento com barra, concluídos recolhidos, sugestões do catálogo. |
| `GET /app/catalogo` | `app.catalogo` | Busca por texto, filtro por categoria e nível. Card indica se já está inscrito. |
| `POST /app/catalogo/{curso}/inscrever` | `app.inscrever` | Um clique, sem página intermediária. Idempotente. |
| `GET /app/c/{curso:slug}` | `app.curso` | Redireciona para a próxima aula não concluída (ou a primeira). |
| `GET /app/c/{curso:slug}/a/{aula:slug}` | `app.aula` | **Sala de aula.** Ver layout abaixo. |
| `POST /app/progresso` | `app.progresso` | Ping do player. `throttle:120,1`. |
| `POST /app/c/{curso}/a/{aula}/concluir` | `app.aula.concluir` | Conclusão manual. |
| `GET /app/materiais/{material}/baixar` | `app.material.baixar` | Stream autenticado. Nunca URL pública. |
| `POST /app/a/{aula}/comentarios` | `app.comentarios.criar` | `throttle:10,1` |
| `GET /app/perfil` · `PUT /app/perfil` | `app.perfil` | Dados, senha, foto, "minhas perguntas". |
| `GET /app/notificacoes` | `app.notificacoes` | Espelha o e-mail. Nada exclusivo daqui. |

### Layout da sala de aula

Duas colunas no desktop, empilhadas no celular (índice **acima** do player em
telas estreitas é errado — índice vai abaixo, colapsado).

**Coluna principal**

1. Player 16:9, `youtube-nocookie`, IFrame API
2. Rótulo `Módulo N · Aula N · MM min` + título da aula
3. Botões: `✓ Marcar como concluída` (primário) e `Próxima aula →`
4. Abas: **Sobre a aula** · **Materiais (n)** · **Perguntas (n)**

**Coluna lateral (320px)**

1. Bloco de progresso: percentual + barra + "6 de 13 aulas"
2. Acordeão de módulos; o módulo da aula atual já aberto
3. Cada aula: ícone de estado (`✓` concluída · `▶` atual · `○` pendente),
   título, duração

### Estados vazios

| Tela | Quando vazia | Mostra |
|---|---|---|
| `/app` | sem matrícula | "Você ainda não está em nenhum treinamento" + botão para o catálogo |
| Aba Perguntas | sem comentário | "Nenhuma pergunta ainda. Seja o primeiro." + campo aberto |
| Aba Materiais | sem anexo | oculta a aba inteira, não mostra aba vazia |
| Catálogo com filtro | sem resultado | "Nenhum curso em <categoria>" + botão limpar filtro |

---

## Área administrativa — `/admin` (Filament)

Middleware do painel + `can:acessar-admin`.

| Recurso | Caminho | Conteúdo |
|---|---|---|
| Painel | `/admin` | Alunos ativos, inscrições em 30 dias, conclusão por curso, aulas com mais abandono, **fila de perguntas sem resposta**. |
| Cursos | `/admin/cursos` | Lista + editor com abas Dados · Conteúdo · Publicação. |
| Construtor de currículo | `/admin/cursos/{id}/curriculo` | Página customizada: arrastar e soltar módulos e aulas, painel lateral de edição da aula. |
| Alunos | `/admin/usuarios` | Filtro por empresa, situação e último acesso. Ficha com cursos, progresso e perguntas. Ações: aprovar, bloquear, matricular, importar CSV. |
| Organizações | `/admin/organizacoes` | CRUD simples. |
| Categorias | `/admin/categorias` | CRUD com ordenação. |
| Moderação | `/admin/comentarios` | Fila única de todos os cursos; responder sem sair da tela; fixar. |
| Relatórios | `/admin/relatorios` | Progresso por curso, por aluno e por empresa. Exportação XLSX. |
| Configurações | `/admin/configuracoes` | Chaves da tabela `configuracoes`, textos de e-mail, termos. |

O **dashboard não é vitrine de números**. O widget que mais importa é a fila de
perguntas sem resposta, porque é o único que cobra ação de alguém.
