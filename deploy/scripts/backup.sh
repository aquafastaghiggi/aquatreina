#!/usr/bin/env bash
set -euo pipefail
umask 077

CONFIG_FILE="/etc/aquafast-treina/backup.env"
if [[ ! -r "$CONFIG_FILE" ]]; then
    echo "Arquivo de configuração ausente: $CONFIG_FILE" >&2
    exit 1
fi

set -a
source "$CONFIG_FILE"
set +a

: "${DB_HOST:?DB_HOST ausente}"
: "${DB_DATABASE:?DB_DATABASE ausente}"
: "${DB_USERNAME:?DB_USERNAME ausente}"
: "${DB_PASSWORD:?DB_PASSWORD ausente}"
: "${BACKUP_DIR:?BACKUP_DIR ausente}"

mkdir -p "$BACKUP_DIR"
DESTINO="$BACKUP_DIR/aquafast-treina-$(date +%Y%m%d-%H%M%S).sql.gz"
MYSQL_PWD="$DB_PASSWORD" mysqldump --single-transaction --quick --lock-tables=false \
    --host="$DB_HOST" --user="$DB_USERNAME" "$DB_DATABASE" | gzip -9 > "$DESTINO"
find "$BACKUP_DIR" -type f -name 'aquafast-treina-*.sql.gz' -mtime +30 -delete
echo "$DESTINO"
