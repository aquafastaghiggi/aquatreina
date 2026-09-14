# Operação do Aquafast Treina

## 1. Ambientes

O desenvolvimento pode rodar no XAMPP. A produção prevista é uma VPS Linux
isolada, com PHP 8.3, MySQL 8, Nginx, Supervisor e cron. O banco deve escutar
somente em `127.0.0.1` ou rede privada e nunca expor a porta 3306 à internet.

O document root do servidor web deve ser exclusivamente a pasta `public/`.
`storage/app/materiais` permanece fora desse caminho e os downloads passam pela
rota autenticada da aplicação.

## 2. Primeiro provisionamento da VPS

1. Instale PHP 8.3 com FPM e extensões exigidas pelo Laravel, MySQL 8, Nginx,
   Supervisor, Composer 2 e Node.js 20.
2. Crie `/var/www/aquafast-treina/current` para o código e
   `/var/www/aquafast-treina/shared/storage` para dados persistentes.
3. Clone `https://github.com/aquafastaghiggi/aquatreina.git` na pasta `current`.
4. Copie `.env.production.example` para `.env`, gere `APP_KEY` com
   `php artisan key:generate` e preencha credenciais somente no servidor.
5. Instale o vhost, worker, cron e logrotate a partir de `deploy/`.
6. Emita o certificado TLS e só então habilite o vhost HTTPS.
7. Execute `deploy/scripts/deploy.sh`.
8. Confirme `/saude`, os logs, a fila e o scheduler.

O branch `main` desse repositório é a fonte oficial da VPS. Cada publicação
executa `git pull --ff-only origin main`; divergências locais interrompem o
deploy em vez de sobrescrever arquivos. Alterações de aplicação devem nascer
no repositório e nunca ser editadas diretamente no servidor.

## 3. Variáveis de ambiente

| Grupo | Variáveis | Finalidade |
|---|---|---|
| Aplicação | `APP_NAME`, `APP_VERSION`, `APP_ENV`, `APP_KEY`, `APP_DEBUG`, `APP_URL` | Identidade, criptografia e URL canônica |
| Idioma | `APP_LOCALE`, `APP_FALLBACK_LOCALE`, `APP_TIMEZONE` | Português e fuso de São Paulo |
| Banco | `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | Conexão MySQL privada |
| Sessão | `SESSION_DRIVER`, `SESSION_LIFETIME`, `SESSION_ENCRYPT`, `SESSION_SECURE_COOKIE`, `SESSION_HTTP_ONLY`, `SESSION_SAME_SITE` | Sessão segura |
| Processamento | `QUEUE_CONNECTION`, `CACHE_STORE` | Filas e cache em banco |
| E-mail | `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_ENCRYPTION`, `MAIL_FROM_ADDRESS`, `MAIL_FROM_NAME` | SMTP corporativo |
| Vídeo | `YOUTUBE_API_KEY` | Metadados; sem chave, usa oEmbed |
| Regras | `TREINA_PERCENTUAL_CONCLUSAO`, `TREINA_INTERVALO_PING`, `TREINA_APROVACAO_MANUAL` | Valores iniciais das configurações |
| Primeiro acesso | `ADMIN_NOME`, `ADMIN_EMAIL`, `ADMIN_PASSWORD` | Seed do administrador inicial |

Em produção, use obrigatoriamente `APP_ENV=production`, `APP_DEBUG=false`, URL
HTTPS e cookies seguros. Nunca envie `.env`, `backup.env` ou dumps ao Git.

## 4. Rotina administrativa

### Aprovar aluno

Abra **Administração → Usuários**, consulte os dados e use **Aprovar**. Para
bloquear acesso, use **Bloquear**. Ambas as operações são auditadas.

### Publicar curso

Abra **Cursos**, preencha responsável, categoria, conteúdo e capa. No
construtor, confira módulos, aulas publicadas, vídeo e duração. A publicação é
recusada enquanto houver pendências estruturais.

### Moderar pergunta

Abra **Comentários**. Perguntas pendentes ficam no topo. Aprove, responda ou
oculte conforme necessário; a resposta oficial notifica o aluno.

### Exportar relatório

Abra **Relatórios**, selecione curso, aluno ou organização e use **Exportar
XLSX**. Exportações acima de 1.000 linhas entram na fila e chegam por e-mail.

### Importar alunos

Em **Usuários**, selecione **Importar CSV**, baixe o modelo, envie o arquivo e
revise a prévia. Linhas inválidas ficam destacadas. Após confirmar, acompanhe o
relatório final recebido pelo administrador.

## 5. Fila e scheduler

O Supervisor mantém workers das filas `emails` e `default`. Para verificar:

```bash
sudo supervisorctl status aquafast-treina-worker:*
php artisan queue:monitor emails,default --max=100
php artisan schedule:list
```

Se a fila travar:

1. confira `storage/logs/laravel.log` e `storage/logs/worker.log`;
2. execute `php artisan queue:failed`;
3. corrija a causa e use `php artisan queue:retry <id>`;
4. reinicie com `php artisan queue:restart` e confirme no Supervisor.

Nunca apague jobs falhos antes de registrar a causa.

## 6. Backup e restauração

Instale `deploy/scripts/backup.sh` no cron diário. O script usa
`/etc/aquafast-treina/backup.env`, que deve pertencer ao root e ter permissão
`600`. A retenção automática é de 30 dias. Copie os backups para um destino
externo à VPS.

Teste a restauração em um banco vazio e isolado:

```bash
deploy/scripts/restore.sh /var/backups/aquafast-treina/arquivo.sql.gz --confirmar-restauracao
php artisan migrate:status
php artisan treina:conferir-progresso
```

Registre data, arquivo, duração, responsável e resultado do teste. Nunca teste
restauração sobrescrevendo produção.

## 7. Checklist de publicação

- confirmar que SMTP, SPF e DKIM passaram nos testes de Gmail, Outlook e domínio corporativo;
- obter aprovação jurídica das versões vigentes de termos e privacidade;
- manter `aprovacao_manual` habilitado;
- criar contas nominais de admin e instrutor, sem compartilhar senha;
- executar o fluxo real: cadastro, verificação, aprovação, aula, material e pergunta;
- verificar `/saude` pelo monitor externo;
- comprovar reinício automático do worker;
- comprovar uma restauração de backup em ambiente isolado.

## 8. Incidentes e LGPD

Para solicitação de exclusão, o administrador usa **Anonimizar usuário** na
ficha do aluno. Não exclua diretamente registros de matrícula ou progresso.
Registre incidentes de indisponibilidade, e-mail, acesso ou exposição de dados
com horário, impacto, ação executada e responsável.
