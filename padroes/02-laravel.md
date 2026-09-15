# 02 — Padrões Laravel

## Onde mora a regra

```
Rota → Controller / Componente Livewire → Ação → Model
                    ↑                       ↑
               FormRequest              Evento/Listener
```

- **Controller e componente Livewire** só orquestram: validam entrada,
  autorizam, chamam a ação, devolvem resposta. Máximo ~20 linhas por método.
- **Ação** contém a regra. É onde o teste bate.
- **Model** guarda relacionamento, cast, scope e acessor. Nunca regra de
  processo.
- **Evento** comunica que algo aconteceu; **listener** reage.

Query de listagem pode ficar em scope no model. Query com decisão de negócio
vai para a ação.

## Models

- `$fillable` explícito. Nunca `$guarded = []`.
- `casts()` como método (Laravel 11+), não propriedade.
- Enums PHP com cast. Nada de string solta comparada com `==`.
- Relacionamentos tipados com o tipo de retorno.
- `SoftDeletes` em `usuarios`, `cursos`, `aulas`, `comentarios`. As demais, não.
- Scopes úteis: `Curso::publicados()`, `Aula::publicadas()`,
  `Comentario::visiveis()`.

```php
protected function casts(): array
{
    return [
        'situacao' => SituacaoCurso::class,
        'publicado_em' => 'datetime',
        'moderar_comentarios' => 'boolean',
    ];
}
```

## Enums

Todo enum de banco vira `string` na coluna e Enum PHP no código, com
`backed enum` e um método `rotulo()` para a interface.

```php
enum SituacaoCurso: string
{
    case Rascunho = 'rascunho';
    case Publicado = 'publicado';
    case Arquivado = 'arquivado';

    public function rotulo(): string
    {
        return match ($this) {
            self::Rascunho => 'Rascunho',
            self::Publicado => 'Publicado',
            self::Arquivado => 'Arquivado',
        };
    }
}
```

Enums da v1: `SituacaoUsuario`, `SituacaoCurso`, `SituacaoAula`,
`SituacaoMatricula`, `SituacaoComentario`, `OrigemMatricula`, `NivelCurso`,
`TipoOrganizacao`, `ProvedorVideo`.

## Validação

Sempre `FormRequest`, nunca `$request->validate()` no controller. Mensagens em
`lang/pt_BR/validation.php`. Regra de autorização vai na Policy, não no
`authorize()` do request — o `authorize()` só delega.

## Autorização

- Uma Policy por model relevante.
- Controller chama `$this->authorize(...)` ou `Gate::authorize(...)`.
- Componente Livewire autoriza no `mount()`, não só no render.
- Filament usa as mesmas Policies.
- **Nunca** confie em esconder o botão.

## Eventos

| Evento | Disparado quando | Listeners |
|---|---|---|
| `AlunoMatriculado` | matrícula criada | e-mail de boas-vindas ao curso |
| `AulaConcluida` | `concluido_em` gravado | recalcular progresso; verificar conclusão do curso |
| `CursoConcluido` | 100% das aulas | notificação; (futuro) certificado |
| `AulaPublicada` | aula sai de rascunho | recalcular caches; avisar matriculados |
| `ComentarioRespondido` | `e_resposta` criado | notificação + e-mail ao autor |

Listener que manda e-mail implementa `ShouldQueue`. Listener que recalcula
cache roda síncrono — o aluno precisa ver a barra mexer.

## Filas

- Driver `database`.
- Job com `$tries = 3` e `backoff` progressivo.
- Nada de job com payload de model inteiro; passe o `id`.
- Job de e-mail na fila `emails`; o resto em `default`.

## Cache

| Chave | TTL | Invalidado por |
|---|---|---|
| `catalogo:publicados` | 10 min | publicar/arquivar curso |
| `curso:{id}:curriculo` | 1 h | qualquer mudança em módulo/aula |
| `video:metadados:{provedor}:{id}` | 24 h | — |

Não cachear progresso: ele já é denormalizado em `matriculas`.

## Consultas

- `with()` sempre que a view percorre relacionamento. N+1 é bug, não lentidão.
- `select()` explícito em listagem grande.
- Nada de `all()` em tabela de crescimento livre.
- Paginação obrigatória em catálogo, moderação e relatórios.

## Tratamento de erro

- Exceptions de domínio em `app/Excecoes/`, com mensagem em português pronta
  para interface (`MatriculaInexistente`, `CursoNaoPublicado`).
- Erro esperado vira mensagem amigável; erro inesperado vai para o log com
  contexto.
- Nunca `try/catch` engolindo silenciosamente.

## Configuração

`config/treina.php` reúne tudo que é regra ajustável:

```php
return [
    'percentual_conclusao' => (int) env('TREINA_PERCENTUAL_CONCLUSAO', 90),
    'intervalo_ping' => (int) env('TREINA_INTERVALO_PING', 10),
    'limite_comentarios_minuto' => 10,
    'upload' => ['max_mb' => 20, 'tipos' => ['pdf', 'xlsx', 'docx', 'pptx', 'png', 'jpg', 'zip']],
];
```

`env()` só dentro de `config/`. Nunca no resto do código.
