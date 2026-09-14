# 01 — Código e nomenclatura

## Regra geral

**Domínio em português sem acento. Framework em inglês.**

A fronteira é simples: se o nome é escolha nossa, é português. Se é API do
Laravel, do PHP ou de um pacote, fica como está.

| Em português | Em inglês (framework) |
|---|---|
| tabelas, colunas, enums | `created_at`, `updated_at`, `deleted_at` |
| models, propriedades, métodos nossos | `up()`, `down()`, `handle()`, `render()`, `boot()` |
| rotas e nomes de rota | `password`, `remember_token`, `email_verified_at` |
| variáveis, parâmetros | `id` |
| views, componentes | nomes de métodos Eloquent (`where`, `hasMany`) |
| textos de interface | |

Sem acento e sem cedilha em identificadores: `situacao`, `organizacoes`,
`matriculas`, `descricao`, `posicao`. Acento só em string de interface.

## Nomes por tipo de arquivo

| Tipo | Convenção | Exemplo |
|---|---|---|
| Model | singular, PascalCase | `Curso`, `Aula`, `Matricula`, `ProgressoAula` |
| Tabela | plural, snake_case | `cursos`, `aulas`, `matriculas`, `progresso_aulas` |
| Migration | `create_<tabela>_table` | `2026_09_14_000300_create_cursos_table.php` |
| Controller | sufixo `Controller` | `SalaDeAulaController` |
| Ação | verbo no infinitivo | `MatricularAluno`, `ConcluirAula`, `RecalcularProgresso` |
| Serviço | substantivo | `ExtratorIdVideo`, `LeitorMetadadosVideo` |
| Policy | `<Model>Policy` | `CursoPolicy` |
| Request | verbo + substantivo + `Request` | `CriarComentarioRequest` |
| Enum | singular, PascalCase | `SituacaoCurso`, `SituacaoMatricula`, `OrigemMatricula` |
| Evento | particípio | `AulaConcluida`, `CursoPublicado`, `AlunoMatriculado` |
| Listener | verbo + `Ao` + evento | `RecalcularProgressoAoConcluirAula` |
| Job | verbo | `EnviarAvisoDeResposta`, `ConferirProgressoDiario` |
| Livewire (aluno) | `App\Livewire\Aluno\` | `SalaDeAula`, `Catalogo`, `Painel` |
| Filament Resource | `<Model>Resource` | `CursoResource` |
| Teste | `<assunto>_test.php` | `concluir_aula_test.php` |

## Estrutura de pastas

```
app/
├── Acoes/                  # regra de negocio (uma classe, um executar())
│   ├── Matricula/
│   ├── Progresso/
│   └── Curso/
├── Enums/
├── Eventos/
├── Filament/
│   ├── Pages/
│   ├── Resources/
│   └── Widgets/
├── Http/
│   ├── Controllers/
│   ├── Middleware/
│   └── Requests/
├── Livewire/
│   ├── Aluno/
│   └── Publico/
├── Listeners/
├── Models/
├── Policies/
├── Servicos/
│   └── Video/
└── Suporte/                # helpers, value objects
```

`app/Acoes/` e `app/Servicos/` é a distinção que importa:
**ação** muda estado do sistema; **serviço** traduz, calcula ou conversa com
fora, sem decidir nada de negócio.

## Ações

Uma classe, um método público `executar()`. Sem `__invoke` (dificulta ler a
stack trace). Dependências por construtor.

```php
final class ConcluirAula
{
    public function executar(Matricula $matricula, Aula $aula, bool $manual = false): ProgressoAula
    {
        // ...
    }
}
```

Ação não recebe `Request` e não devolve `Response`. Isso é papel do controller.

## Estilo

- `declare(strict_types=1)` no topo de todo arquivo PHP.
- Tipos em tudo: parâmetro, retorno, propriedade.
- `final` por padrão em ações, serviços e enums. Models não.
- Sem facade dentro de ação; injete o que precisa.
- `match` no lugar de `switch`.
- Early return em vez de `else` aninhado.
- Máximo ~120 colunas. Pint com preset `laravel` decide o resto.

## Comentários

Comentário explica **por que**, nunca **o que**. Em português.

```php
// Salto de barra: registra a posicao alcancada, mas nao conta como assistido (RN-02).
```

Sem PHPDoc que só repete a assinatura. PHPDoc só para generics de coleção.

## Números mágicos

Nenhum. Vão para `config/treina.php`, lido do `.env`. Lista em
`docs/04-regras-de-negocio.md`.

## Textos

Interface em `lang/pt_BR/`. Nada de string solta na Blade para mensagem de
validação ou de erro. Rótulo curto pode ficar inline.
