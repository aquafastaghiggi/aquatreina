# Etapa 6 — Entrega e deploy

> Objetivo: a plataforma em produção, com o curso piloto publicado e a operação
> sabendo o que fazer.

**Pré-requisitos:** etapas 0–5 aprovadas.
**Duração estimada:** 3–5 dias.
**Leia antes:** `docs/06-seguranca-e-lgpd.md` inteiro.

**Decisão que precisa estar respondida:** A-07 (onde hospedar).

---

## Tarefas

### 6.1 Hardening

- [ ] `APP_DEBUG=false`, `APP_ENV=production`
- [ ] HTTPS obrigatório, HSTS ligado
- [ ] CSP permitindo `youtube-nocookie.com` e `i.ytimg.com`; `frame-src`
      restrito a esses
- [ ] `X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy`
- [ ] Cookies `secure`, `httponly`, `samesite=lax`
- [ ] Rate limit conferido em: login, cadastro, progresso, comentários
- [ ] `storage/app/materiais` fora do alcance do servidor web
- [ ] Banco sem porta exposta à internet
- [ ] Varredura: nenhuma chave, token ou senha versionada

### 6.2 Desempenho

- [ ] `config:cache`, `route:cache`, `view:cache`, `event:cache` no deploy
- [ ] `composer install --no-dev --optimize-autoloader`
- [ ] Assets compilados (`npm run build`)
- [ ] Laravel Debugbar removido das dependências de produção
- [ ] Auditoria de N+1 em `/app`, `/app/catalogo` e na sala de aula
- [ ] Índices de `docs/02-modelo-de-dados.md` conferidos no banco de produção

### 6.3 Infraestrutura

- [ ] Servidor provisionado conforme A-07 (recomendado: isolado da intranet)
- [ ] PHP 8.3, MySQL 8, Nginx
- [ ] Supervisor rodando `queue:work` com restart automático
- [ ] Cron do `schedule:run`
- [ ] Backup diário do banco, retenção 30 dias
- [ ] **Restauração testada pelo menos uma vez** antes do go-live
- [ ] Rotação de log configurada
- [ ] Monitoramento apontando para `/saude`

### 6.4 E-mail

- [ ] SMTP corporativo configurado
- [ ] SPF e DKIM do domínio novo conferidos **antes** do primeiro envio em massa
- [ ] Teste de entrega para Gmail, Outlook e um domínio corporativo
- [ ] Remetente e assinatura revisados

### 6.5 Conteúdo e go-live

- [ ] Curso piloto publicado e revisado de ponta a ponta
- [ ] Textos de `/termos` e `/privacidade` aprovados pelo jurídico
- [ ] Cadastro confirmado como pendente até aprovação do admin
- [ ] Usuários admin e instrutor criados
- [ ] Teste de aceitação: um usuário real se cadastra, é aprovado, assiste uma
      aula inteira, baixa um material e faz uma pergunta

### 6.6 Documentação de operação

- [ ] `README.md` do repositório: como rodar local, como fazer deploy
- [ ] `docs/operacao.md`: como aprovar aluno, publicar curso, moderar pergunta,
      exportar relatório, o que fazer se a fila travar
- [ ] Lista de variáveis de `.env` explicadas
- [ ] Como restaurar o backup

---

## Testes obrigatórios

- [ ] smoke de todas as rotas públicas (200)
- [ ] smoke de todas as rotas do aluno com usuário autenticado
- [ ] `/admin` responde para admin e nega para aluno
- [ ] suíte completa verde no ambiente de produção-espelho

---

## Critérios de aceite

1. Um usuário real percorre todo o fluxo em produção sem ajuda.
2. E-mail chega na caixa de entrada, não no spam.
3. Fila processando e reiniciando sozinha após queda.
4. Backup restaurado com sucesso em ambiente de teste.
5. `APP_DEBUG=false` e nenhum dado sensível versionado.
6. `docs/operacao.md` permite a alguém de fora do projeto operar a plataforma.

---

## Depois do go-live

Acompanhe por 30 dias, contra as métricas de `docs/00-visao-geral.md`:

- taxa de conclusão por curso
- tempo entre cadastro e primeira aula concluída
- perguntas sem resposta há mais de 3 dias
- taxa de cadastro falso

Só depois disso abra `etapas/99-backlog-pos-mvp.md`.
