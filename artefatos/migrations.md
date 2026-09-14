# Migrations — ordem e assinatura

Ordem obrigatória: as chaves estrangeiras dependem dela. O conteúdo exato de
cada tabela está em `artefatos/schema.sql`.

## Etapa 0

| # | Arquivo | Observação |
|---|---|---|
| 1 | `..._create_organizacoes_table.php` | vem antes de `usuarios` (FK) |
| 2 | `..._create_usuarios_table.php` | **substitui** `create_users_table`. Ver `padroes/04-banco-e-migrations.md` §2 |
| 3 | framework | `sessions`, `cache`, `jobs`, `password_reset_tokens` — geradas pelo Laravel, nomes mantidos |
| 4 | pacote | `permission_tables` (spatie) |
| 5 | pacote | `create_activity_log_table` (spatie) |
| 6 | framework | `notifications` |
| 7 | `..._create_configuracoes_table.php` | |

## Etapa 1

| # | Arquivo |
|---|---|
| 8 | `..._create_categorias_table.php` |
| 9 | `..._create_cursos_table.php` |
| 10 | `..._create_modulos_table.php` |
| 11 | `..._create_aulas_table.php` |
| 12 | `..._create_materiais_table.php` |

## Etapa 2

| # | Arquivo |
|---|---|
| 13 | `..._create_matriculas_table.php` |

`matriculas.ultima_aula_id` referencia `aulas`, criada na etapa 1.
Use `ON DELETE SET NULL`: apagar aula não pode apagar matrícula.

## Etapa 3

| # | Arquivo |
|---|---|
| 14 | `..._create_progresso_aulas_table.php` |

## Etapa 4

| # | Arquivo |
|---|---|
| 15 | `..._create_comentarios_table.php` |

---

## Armadilha das chaves estrangeiras

`constrained()` sem argumento deduz a tabela pelo nome da coluna e vai procurar
`users`, `curso`, `aula`. **Sempre passe o nome explicitamente:**

```php
$table->foreignId('usuario_id')->constrained('usuarios')->cascadeOnDelete();
$table->foreignId('curso_id')->constrained('cursos')->cascadeOnDelete();
$table->foreignId('modulo_id')->constrained('modulos')->cascadeOnDelete();
$table->foreignId('aula_id')->constrained('aulas')->cascadeOnDelete();
$table->foreignId('matricula_id')->constrained('matriculas')->cascadeOnDelete();
$table->foreignId('categoria_id')->nullable()->constrained('categorias')->nullOnDelete();
$table->foreignId('organizacao_id')->nullable()->constrained('organizacoes')->nullOnDelete();
$table->foreignId('responsavel_id')->constrained('usuarios')->restrictOnDelete();
$table->foreignId('ultima_aula_id')->nullable()->constrained('aulas')->nullOnDelete();
$table->foreignId('comentario_pai_id')->nullable()->constrained('comentarios')->cascadeOnDelete();
```

## Política de ON DELETE

| Relação | Regra | Por quê |
|---|---|---|
| curso → módulo → aula → material | `CASCADE` | apagar curso apaga a estrutura |
| matrícula → progresso | `CASCADE` | progresso não existe sem matrícula |
| usuário → matrícula | `CASCADE` | mas usuário é anonimizado, não apagado |
| curso.responsavel_id | `RESTRICT` | não se apaga instrutor com curso |
| curso.categoria_id | `SET NULL` | curso sobrevive sem categoria |
| usuario.organizacao_id | `SET NULL` | aluno sobrevive sem empresa |
| matricula.ultima_aula_id | `SET NULL` | apagar aula não pode apagar matrícula |

## Enums

Nunca o tipo `ENUM` do MySQL. Sempre `string` com `comment` listando os valores,
e Enum PHP no model. Alterar um `ENUM` de MySQL em tabela cheia é `ALTER TABLE`
com lock.

## Regra permanente

**Migration versionada não se reescreve.** Vale a partir da etapa 1. Mudança
depois é `add_..._to_..._table` ou `alter_...`.
