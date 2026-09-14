# 03 — Livewire e Filament

## Livewire (área do aluno)

### Quando usar

Livewire para tela com estado (catálogo com filtro, sala de aula, perfil).
Blade puro para página estática (vitrine, termos). Não transforme tudo em
componente.

### Regras

- Componentes em `app/Livewire/Aluno/` e `app/Livewire/Publico/`.
- Propriedade pública **só** o que a view usa. O resto é privado ou computado.
- Model inteiro como propriedade pública só com `#[Locked]`. Preferir o `id`.
- `#[Computed]` para o que deriva de outra coisa — não guarde derivado em
  propriedade pública.
- `wire:key` em todo item de laço. Sem exceção.
- Autorização no `mount()`.
- `wire:model.live` só onde a reatividade imediata importa (busca com debounce).
  No resto, `wire:model.blur`.
- Nada de query pesada em `render()` sem cache ou `#[Computed]`: `render()` roda
  a cada interação.

### Componentes da v1

| Componente | Rota | Responsabilidade |
|---|---|---|
| `Publico\Vitrine` | `/` | prateleira de cursos publicados |
| `Publico\PaginaCurso` | `/cursos/{slug}` | ementa e inscrição |
| `Aluno\Painel` | `/app` | continue de onde parou, em andamento, sugestões |
| `Aluno\Catalogo` | `/app/catalogo` | busca e filtro |
| `Aluno\SalaDeAula` | `/app/c/{curso}/a/{aula}` | player, índice, abas |
| `Aluno\AbaComentarios` | filho | lista + envio de pergunta |
| `Aluno\Perfil` | `/app/perfil` | dados e minhas perguntas |

### O player não é Livewire

O rastreamento de progresso é JS puro (`resources/js/player-aula.js`) falando
com `POST /app/progresso`. Não faça o ping passar por Livewire: um `setInterval`
disparando round-trip de componente a cada 10 segundos re-renderiza a tela
inteira.

A comunicação de volta (marcar a aula como concluída no índice) é via evento do
navegador que o componente escuta com `#[On]`.

---

## Filament (admin)

### Regras

- Painel em `/admin`, id `admin`.
- **Usa as mesmas Policies do app.** Nenhuma regra de acesso duplicada.
- Um `Resource` por model administrável: `Curso`, `Usuario`, `Organizacao`,
  `Categoria`, `Comentario`.
- Ação que muda estado chama a **Ação** de `app/Acoes/`. O Resource não
  implementa regra.
- Rótulos e mensagens em português, definidos no Resource
  (`getModelLabel`, `getPluralModelLabel`).
- Tabela grande com `deferLoading()` e filtros indexados.

### Estrutura do CursoResource

Formulário em abas:

| Aba | Campos |
|---|---|
| **Dados** | título, slug, subtítulo, categoria, nível, responsável, descrição, capa |
| **Conteúdo** | link para o construtor de currículo (a edição real acontece lá) |
| **Publicação** | situação, moderar comentários, posição, publicado em |

A ação **Publicar** chama `PublicarCurso`, que valida a RN-09 e devolve **toda**
a lista de pendências de uma vez.

### Construtor de currículo

A única tela que não cabe no Filament padrão.
`app/Filament/Pages/ConstrutorCurriculo.php`, rota
`/admin/cursos/{registro}/curriculo`.

- Coluna esquerda: módulos e aulas com arrastar e soltar (SortableJS), salvando
  `posicao` numa chamada só ao soltar.
- Coluna direita: edição da aula selecionada — título, descrição, **link do
  YouTube** (cola e o sistema lê título e duração), materiais, amostra gratuita.
- Feedback explícito quando a duração não pôde ser lida: campo manual visível,
  com aviso de que a conclusão automática depende dele.

Essa é a tela onde o instrutor passa o tempo dele. Vale caprichar.

### Widgets do painel

Ordem importa. O primeiro é o que cobra ação:

1. **Perguntas sem resposta** (contador + link para a fila)
2. Alunos ativos / inscrições nos últimos 30 dias
3. Conclusão por curso (barra por curso publicado)
4. Aulas com maior abandono (aulas onde mais gente parou)

Nada de gráfico bonito sem decisão associada.
