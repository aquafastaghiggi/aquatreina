# 07 — Design e interface

Sem manual de marca confirmado (decisão A-06). Enquanto não houver, vale o que
está aqui. Se o manual aparecer, ele vence.

---

## Princípio

A referência do projeto é a área de membros brasileira: player mandando na
tela, índice lateral, conclusão automática. Copie a **ergonomia**, não a
retórica. Nada de contagem regressiva, selo de bônus, ranking ou "libera
amanhã". O público é profissional e já comprou o produto.

Aquafast na frente; instrutor discreto.

## Tokens

Tema **escuro por padrão** na área do aluno — o vídeo é o ponto de luz da tela.

```
--fundo         #0B1719   fundo da aplicacao
--superficie    #112226   cards, barra lateral
--superficie-2  #172E33   hover, campos
--linha         #23403F   bordas
--texto         #E6EFED   texto principal
--texto-2       #A9C1BF   secundario
--texto-3       #7C9A99   legendas, metadados
--marca         #4FBFC6   acao primaria, progresso, aula atual
--marca-suave   #12363A   fundo de destaque
--sucesso       #79C79B   aula concluida
--atencao       #DD8E58   pendencia, aviso
```

Área pública usa a mesma paleta. Admin fica no tema padrão do Filament — não
gaste tempo tematizando o painel na v1.

## Tipografia

- Interface: system stack (`-apple-system, Segoe UI, Roboto, sans-serif`).
  Sem webfont na v1: o representante abre isso em 4G ruim.
- Números de progresso e duração: `font-variant-numeric: tabular-nums`.
- Título de aula no máximo 2 linhas, com `text-wrap: balance`.

## Escala e espaçamento

Escala do Tailwind, sem customização. Raio `rounded-lg` em cards e `rounded-md`
em botões. Uma sombra só, discreta, reservada ao card de "continue de onde
parou" — se tudo tem sombra, nada tem destaque.

## Responsivo

Ponto de corte em `lg` (1024px).

| Elemento | Desktop | Celular |
|---|---|---|
| Sala de aula | 2 colunas, índice à direita 320px | 1 coluna; player primeiro, **índice colapsado abaixo** |
| Painel | grade de 3 cards | 1 coluna |
| Catálogo | 3–4 cards por linha | 1 card, filtro em gaveta |
| Admin | Filament padrão | Filament padrão |

Índice acima do player em tela estreita é erro. O aluno abriu o link para
assistir, não para escolher.

## Componentes que se repetem

- **Barra de progresso:** 5px de altura, `--linha` de trilho, `--marca` de
  preenchimento, sempre acompanhada do número em texto. Barra sozinha não
  comunica.
- **Item de aula no índice:** ícone de estado · título · duração à direita em
  `tabular-nums`. Aula atual com fundo `--marca-suave` e borda `--marca`.
- **Card de curso:** capa 16:10 · título (2 linhas) · categoria · duração total ·
  barra de progresso se matriculado.
- **Estado vazio:** frase curta + uma ação. Nunca só a frase.

## Acessibilidade — mínimo obrigatório

- Contraste AA no texto.
- Foco visível em tudo que é focável. Não remova `outline` sem substituir.
- Índice do curso navegável por teclado; acordeão com `aria-expanded`.
- Player com `title` descritivo no iframe.
- `prefers-reduced-motion` respeitado.
- Estado de aula não pode ser comunicado **só** por cor: ícone junto.

## Textos da interface

Português do Brasil, segunda pessoa direta, sem "você poderá".

| Em vez de | Escreva |
|---|---|
| "Submeter" | "Enviar" |
| "Erro ao processar sua solicitação" | "Não foi possível salvar. Tente de novo em alguns segundos." |
| "Curso matriculado com sucesso" | "Pronto. Bons estudos." |
| "Nenhum registro encontrado" | "Nenhum curso em Operação. Limpar filtro." |

Botão diz o que acontece; a confirmação usa o mesmo verbo no passado.
