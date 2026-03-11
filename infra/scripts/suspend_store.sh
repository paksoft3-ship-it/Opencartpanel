#!/usr/bin/env bash
set -euo pipefail

CLIENT_CODE="${1:-}"

if [[ -z "$CLIENT_CODE" ]]; then
  echo "ERROR: CLIENT_CODE is required"
  exit 1
fi

META_FILE="/var/www/stores/${CLIENT_CODE}/novakur_meta.json"

if [[ ! -f "$META_FILE" ]]; then
  echo "ERROR: meta file not found: $META_FILE"
  exit 1
fi

DB_NAME="$(sed -n 's/.*"db_name"[[:space:]]*:[[:space:]]*"\([^"]*\)".*/\1/p' "$META_FILE" | head -n1)"
DB_USER="$(sed -n 's/.*"db_user"[[:space:]]*:[[:space:]]*"\([^"]*\)".*/\1/p' "$META_FILE" | head -n1)"

if [[ -z "$DB_NAME" || -z "$DB_USER" ]]; then
  echo "ERROR: db_name or db_user missing in meta"
  exit 1
fi

mysql "$DB_NAME" -e "UPDATE oc_setting SET value = '0' WHERE store_id = 0 AND \\`key\\` = 'config_status';"

echo "SUSPENDED client=${CLIENT_CODE}"
