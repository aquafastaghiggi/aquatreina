# Arquivos para a VPS

Estes arquivos são modelos versionáveis, sem credenciais. O destino previsto é
uma VPS Ubuntu/Debian isolada, com PHP 8.3, MySQL 8 e Nginx.

- `nginx/`: vhost HTTPS; ajuste domínio e caminhos antes de ativar.
- `supervisor/`: dois workers para as filas `emails` e `default`.
- `cron/`: execução do scheduler a cada minuto.
- `logrotate/`: retenção e compactação dos logs.
- `scripts/deploy.sh`: instalação otimizada, build, migration e caches.
- `scripts/backup.sh` e `restore.sh`: backup com retenção de 30 dias e restauração explícita.

Copie `backup.env.example` para `/etc/aquafast-treina/backup.env`, restrinja-o
com `chmod 600` e preencha os valores somente no servidor.
