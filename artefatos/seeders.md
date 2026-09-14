# Seeders e dados de exemplo

## `UsuarioAdminSeeder` — todos os ambientes

Cria o primeiro admin. **Credenciais do `.env`**, nunca fixas no código:

```
ADMIN_NOME="Andre Ghiggi"
ADMIN_EMAIL="aghiggi@aquafast.com.br"
ADMIN_SENHA=
```

Se `ADMIN_SENHA` estiver vazio, gera uma senha aleatória e a imprime **uma vez**
no console. Idempotente: rodar de novo não duplica nem troca a senha.

## `CategoriaSeeder` — todos os ambientes

| Nome | Slug | Cor |
|---|---|---|
| Produtos | `produtos` | `#4FBFC6` |
| Operação | `operacao` | `#79C79B` |
| Comercial | `comercial` | `#DD8E58` |
| Qualidade | `qualidade` | `#A9C1BF` |

## `ConfiguracaoSeeder` — todos os ambientes

As quatro chaves do bloco `INSERT` de `artefatos/schema.sql`. Idempotente por
`chave`.

## `CursoDemoSeeder` — **só em `local` e `testing`**

Guarde atrás de `if (! app()->environment(['local', 'testing'])) return;`.

Conteúdo:

- **2 cursos publicados**
  - *Linha Garrafão 20 L — operação e atendimento* (Operação, básico)
    3 módulos, 9 aulas
  - *Argumentário de vendas — PET 500 ml* (Comercial, intermediário)
    2 módulos, 5 aulas
- **1 curso em rascunho**, para testar que não aparece no catálogo
- **1 curso arquivado**, com uma matrícula ativa, para testar a RN-10
- **1 instrutor** e **5 alunos**, com progresso variado:
  - um em 0%
  - um em ~46% (para conferir o "continue de onde parou")
  - um com curso concluído
  - um com matrícula cancelada
  - um `pendente` de aprovação
- **materiais** em 3 aulas (PDFs de exemplo gerados pelo seeder, não versionados)
- **comentários**: 4 perguntas, 2 com resposta do instrutor, 1 `pendente`, 1 fixada

Os IDs de vídeo devem ser de vídeos **públicos e reais** para que o player
funcione no ambiente local. Enquanto não houver vídeos da Aquafast, use IDs
públicos quaisquer e marque no comentário do seeder:

```php
// PLACEHOLDER: trocar pelos videos reais do canal da Aquafast antes da etapa 6.
```

## Factories

Todos os models precisam de factory, com estados úteis:

```php
Curso::factory()->publicado()
Curso::factory()->arquivado()
Aula::factory()->publicada()
Aula::factory()->semDuracao()        // duracao_segundos = 0, testa RN-01
Matricula::factory()->concluida()
Matricula::factory()->cancelada()
Usuario::factory()->pendente()
Usuario::factory()->bloqueado()
Usuario::factory()->instrutor()
Comentario::factory()->pendente()
Comentario::factory()->resposta()
```

Os testes dependem desses estados. Sem eles, cada teste reconstrói o cenário na
mão e a suíte fica ilegível.

## Comandos

```bash
php artisan migrate:fresh --seed     # zera e popula
php artisan db:seed --class=CursoDemoSeeder
```

`migrate:fresh --seed` rodar limpo do zero é critério de aceite de toda etapa
(ver `padroes/07-definition-of-done.md`).
