# 04 — Banco e migrations

## 1. Regras gerais

- Uma migration por tabela na criação. Alteração depois é migration nova.
- **Migration versionada não se reescreve.** Vale a partir da etapa 1.
- Nome: `create_<tabela>_table`, `add_<coluna>_to_<tabela>_table`,
  `alter_<tabela>_<o_que>`.
- `down()` sempre implementado e coerente.
- Toda FK com `ON DELETE` explícito. Escolha consciente, não padrão do MySQL.
- Enum vira `string` com comentário listando os valores. **Não** use o tipo
  `enum` do MySQL: alterar depois exige `ALTER TABLE` em tabela cheia.
- `decimal` para dinheiro (não há na v1). `float` em lugar nenhum.
- Índice em toda coluna usada em `where` de listagem.

```php
$table->string('situacao', 20)->default('rascunho')
      ->comment('rascunho|publicado|arquivado');
```

## 2. A tabela `usuarios` — leia antes da etapa 0

O Laravel assume `users`. Como o padrão do projeto é português (D-07), a tabela
chama `usuarios` e o model é `Usuario`. Isso exige **cinco** ajustes. Pular
qualquer um deles quebra login ou reset de senha, às vezes só em produção.

1. **Migration.** Renomeie a migration padrão para
   `create_usuarios_table` e a tabela para `usuarios`, com as colunas de
   `docs/02-modelo-de-dados.md`.

2. **`config/auth.php`.**
   ```php
   'providers' => [
       'users' => [
           'driver' => 'eloquent',
           'model' => App\Models\Usuario::class,
       ],
   ],
   ```
   A **chave** `users` do array permanece — é referência interna do framework.
   Só o model muda.

3. **`password_reset_tokens`.** A migration padrão cria essa tabela e ela
   referencia e-mail, não id. Mantenha o nome da tabela. Confirme
   `config/auth.php → passwords.users.table`.

4. **Chaves estrangeiras.** Toda FK aponta para `usuarios`:
   ```php
   $table->foreignId('usuario_id')->constrained('usuarios')->cascadeOnDelete();
   ```
   `constrained()` sem argumento tentaria `users`. **Sempre passe o nome.**

5. **Tabelas de pacotes.** `spatie/laravel-permission` e
   `spatie/laravel-activitylog` usam `model_type` polimórfico e funcionam sem
   ajuste. `sessions` e `notifications` guardam `user_id`/`notifiable_id` —
   mantenha os nomes do framework nessas tabelas.

**Tabelas que permanecem em inglês:** `sessions`, `cache`, `cache_locks`,
`jobs`, `job_batches`, `failed_jobs`, `password_reset_tokens`, `notifications`,
`activity_log`, `migrations`. São propriedade do framework e dos pacotes.

## 3. Colunas do framework mantidas em inglês

`id`, `created_at`, `updated_at`, `deleted_at`, `password`, `remember_token`,
`email`, `email_verified_at`.

`email` fica em inglês porque Fortify, validação e reset dependem do nome.

## 4. Ordem de criação

As FKs exigem ordem. Sequência completa em `artefatos/migrations.md`:

```
organizacoes → usuarios → categorias → cursos → modulos → aulas
→ materiais → matriculas → progresso_aulas → comentarios → configuracoes
```

`matriculas.ultima_aula_id` referencia `aulas`, que já existe nesse ponto.
`ON DELETE SET NULL` nessa FK — apagar aula não pode apagar matrícula.

## 5. Seeders

| Seeder | Conteúdo |
|---|---|
| `UsuarioAdminSeeder` | 1 admin, credenciais do `.env` local. **Nunca** senha fixa no código. |
| `CategoriaSeeder` | Produtos · Operação · Comercial · Qualidade |
| `ConfiguracaoSeeder` | chaves da v1 com valores padrão |
| `CursoDemoSeeder` | **só em local/testing**: 2 cursos, 3 módulos, 9 aulas, 5 alunos, progresso variado |

`CursoDemoSeeder` usa IDs de vídeo reais e públicos do canal da Aquafast quando
existirem; enquanto não existirem, IDs públicos quaisquer, marcados no
comentário do seeder como placeholder.

Factories para todos os models — os testes dependem delas.

## 6. Desempenho

- Toda listagem paginada.
- `total_aulas` e `minutos_estimados` em `cursos` são cache, atualizados por
  listener em mudança de aula.
- `percentual_progresso` em `matriculas` é cache (D-05), com job noturno
  `ConferirProgressoDiario` reconciliando contra `progresso_aulas`.
- Charset `utf8mb4`, collation `utf8mb4_unicode_ci`.
