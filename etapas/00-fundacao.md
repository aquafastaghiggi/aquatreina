# Etapa 0 — Fundação

> Objetivo: um Laravel limpo, com autenticação funcionando, papéis, layout e
> Filament instalado. Nenhuma regra de negócio ainda.

**Pré-requisitos:** nenhum.
**Duração estimada:** 1 semana.
**Leia antes:** `AGENTS.md`, `docs/01-decisoes.md`, `padroes/01-codigo-e-nomenclatura.md`, `padroes/02-laravel.md`,
`padroes/04-banco-e-migrations.md` (§2 é obrigatório).

---

## Tarefas

### 0.1 Projeto

- [ ] `composer create-project laravel/laravel .` (Laravel 12, PHP 8.3+)
- [ ] `.env` a partir de `.env.example` deste pacote; `php artisan key:generate`
- [ ] `config/app.php`: locale `pt_BR`, timezone `America/Sao_Paulo`
- [ ] Arquivos de tradução `lang/pt_BR/` (validation, auth, passwords)
- [ ] `config/treina.php` com as constantes de `docs/04-regras-de-negocio.md`
- [ ] `.gitignore` conferido conforme `padroes/06-git-e-commits.md`
- [ ] `git init`, primeiro commit

### 0.2 Pacotes

Instale exatamente o que está em `artefatos/composer-packages.md`. Nada além.

- [ ] Filament 4, painel `admin` em `/admin`
- [ ] Laravel Fortify
- [ ] spatie/laravel-permission
- [ ] spatie/laravel-activitylog
- [ ] Pest + plugin Laravel
- [ ] Pint

### 0.3 A tabela `usuarios`

**Ponto de maior risco desta etapa.** Siga `padroes/04-banco-e-migrations.md` §2
item por item.

- [ ] Migration `create_usuarios_table` com as colunas de `docs/02-modelo-de-dados.md`
- [ ] Migration `create_organizacoes_table` (vem **antes**, por causa da FK)
- [ ] Model `Usuario` com `$fillable`, `casts()`, `SoftDeletes`
- [ ] Enum `SituacaoUsuario` (`pendente` · `ativo` · `bloqueado`)
- [ ] Enum `TipoOrganizacao`
- [ ] `config/auth.php` apontando para `App\Models\Usuario`
- [ ] `password_reset_tokens` conferida e funcionando
- [ ] `UsuarioFactory`

### 0.4 Autenticação

- [ ] Fortify: login, logout, cadastro, reset de senha, verificação de e-mail
- [ ] Rotas em português (`/entrar`, `/cadastrar`, `/senha/esqueci`) conforme `artefatos/rotas.md`
- [ ] Cadastro coleta: nome, e-mail, telefone, empresa, cargo, aceite de termos
- [ ] Grava `termos_aceitos_em` e `termos_ip`
- [ ] Honeypot e `throttle` de 5 cadastros por IP por hora
- [ ] Verificação de e-mail **obrigatória**
- [ ] Senha com `Password::defaults()->uncompromised()`

### 0.5 Papéis e middleware

- [ ] Papéis `aluno`, `instrutor`, `admin` via spatie
- [ ] Novo cadastro recebe `aluno` automaticamente
- [ ] `situacao` inicial conforme `config('treina.aprovacao_manual')`
- [ ] Middleware `garantir.ativo`: `pendente` → `/aguardando-aprovacao`,
      `bloqueado` → logout com mensagem
- [ ] Middleware `registrar.acesso`: atualiza `ultimo_acesso_em` no máximo 1x/hora (RN-11)
- [ ] Gate `acessar-admin` (papéis `admin` e `instrutor`)

### 0.6 Layout e páginas base

- [ ] Tailwind com os tokens de `docs/07-design-e-ui.md`
- [ ] Layout público e layout do aluno (`/app`), tema escuro
- [ ] Páginas estáticas `/termos` e `/privacidade` (texto placeholder marcado)
- [ ] Página `/aguardando-aprovacao`
- [ ] Páginas de erro 403, 404 e 500 em português

### 0.7 Filament

- [ ] Painel `admin` acessível em `/admin`, protegido pelo Gate
- [ ] `UsuarioResource` mínimo: listar, filtrar por situação, aprovar, bloquear
- [ ] Ações `AprovarUsuario` e `BloquearUsuario` em `app/Acoes/Usuario/`
- [ ] `activity_log` registrando aprovação e bloqueio

### 0.8 Seeders e qualidade

- [ ] `UsuarioAdminSeeder` (credenciais do `.env`, nunca fixas)
- [ ] `ConfiguracaoSeeder`
- [ ] `php artisan migrate:fresh --seed` roda limpo
- [ ] Pint configurado, código formatado
- [ ] Pest configurado, testes da etapa escritos

---

## Testes obrigatórios

- [ ] cadastro cria usuário com papel `aluno` e situação correta
- [ ] cadastro grava `termos_aceitos_em` e `termos_ip`
- [ ] usuário sem e-mail verificado não acessa `/app`
- [ ] usuário `pendente` cai em `/aguardando-aprovacao` (RN-08)
- [ ] usuário `bloqueado` é deslogado (RN-08)
- [ ] aluno não acessa `/admin`
- [ ] `ultimo_acesso_em` não é atualizado duas vezes na mesma hora (RN-11)

---

## Critérios de aceite

1. `php artisan migrate:fresh --seed` roda do zero sem erro.
2. É possível se cadastrar, receber o e-mail (log), verificar e chegar em `/app`.
3. Com `aprovacao_manual = true`, o novo usuário vê `/aguardando-aprovacao`;
   o admin aprova em `/admin` e o acesso libera.
4. Reset de senha funciona ponta a ponta.
5. `/admin` bloqueia aluno e libera admin.
6. `vendor/bin/pint --test` e `php artisan test` verdes.
7. Nenhuma referência à tabela `users` sobrou no código.

## Não faça nesta etapa

Curso, módulo, aula, catálogo, player, integração com YouTube, layout caprichado
da sala de aula. Nada disso existe ainda.
