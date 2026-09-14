#!/usr/bin/env bash
set -euo pipefail

if [[ $# -ne 2 || "$2" != "--confirmar-restauracao" ]]; then
    echo "Uso: $0 /caminho/backup.sql.gz --confirmar-restauracao" >&2
    exit 1
fi

BACKUP_FILE="$1"
CONFIG_FILE="/etc/aquafast-treina/backup.env"
[[ -r "$BACKUP_FILE" ]] || { echo "Backup não encontrado" >&2; exit 1; }
[[ -r "$CONFIG_FILE" ]] || { echo "Configuração não encontrada" >&2; exit 1; }

set -a
source "$CONFIG_FILE"
set +a

: "${DB_HOST:?DB_HOST ausente}"
: "${DB_DATABASE:?DB_DATABASE ausente}"
: "${DB_USERNAME:?DB_USERNAME ausente}"
: "${DB_PASSWORD:?DB_PASSWORD ausente}"

gzip -cd "$BACKUP_FILE" | MYSQL_PWD="$DB_PASSWORD" mysql \
    --host="$DB_HOST" --user="$DB_USERNAME" "$DB_DATABASE"
echo "Restauração concluída em $DB_DATABASE"
