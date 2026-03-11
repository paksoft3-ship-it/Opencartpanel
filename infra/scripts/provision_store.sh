#!/usr/bin/env bash
set -euo pipefail

CLIENT_CODE="${1:-}"
DOMAIN="${2:-}"
EMAIL="${3:-}"
TIER="${4:-}"

if [[ -z "$CLIENT_CODE" || -z "$DOMAIN" ]]; then
  echo "ERROR: CLIENT_CODE and DOMAIN are required"
  exit 1
fi

if [[ ! "$CLIENT_CODE" =~ ^[a-zA-Z0-9_]+$ ]]; then
  echo "ERROR: CLIENT_CODE must be alphanumeric/underscore"
  exit 1
fi

STORE_PATH="/var/www/stores/${CLIENT_CODE}"
GOLDEN_PATH="${GOLDEN_PATH:-/var/www/novakur_golden}"
GOLDEN_SQL="${GOLDEN_PATH}/golden.sql"
DB_NAME="opencart_${CLIENT_CODE}"
DB_USER="nk_${CLIENT_CODE}"
DB_PASS="$(openssl rand -hex 16)"
MYSQL="${MYSQL_BIN:-mysql}"
MYSQLDUMP="${MYSQLDUMP_BIN:-mysqldump}"

if [[ -d "$STORE_PATH" ]]; then
  echo "ERROR: store path already exists: $STORE_PATH"
  exit 1
fi

mkdir -p /var/www/stores
cp -r "$GOLDEN_PATH" "$STORE_PATH"

$MYSQL -e "CREATE DATABASE \\`${DB_NAME}\\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
$MYSQL -e "CREATE USER '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';"
$MYSQL -e "GRANT ALL PRIVILEGES ON \\`${DB_NAME}\\`.* TO '${DB_USER}'@'localhost'; FLUSH PRIVILEGES;"
$MYSQL "$DB_NAME" < "$GOLDEN_SQL"

replace_define() {
  local file="$1"
  local key="$2"
  local value="$3"

  perl -i -pe "s#^define\\('\\Q${key}\\E',\\s*'[^']*'\\);#define('${key}', '${value}');#" "$file"
}

CATALOG_CONFIG="${STORE_PATH}/config.php"
ADMIN_CONFIG="${STORE_PATH}/admin/config.php"

# Catalog config DB and URL constants
replace_define "$CATALOG_CONFIG" "DB_DATABASE" "$DB_NAME"
replace_define "$CATALOG_CONFIG" "DB_USERNAME" "$DB_USER"
replace_define "$CATALOG_CONFIG" "DB_PASSWORD" "$DB_PASS"
replace_define "$CATALOG_CONFIG" "HTTP_SERVER" "https://${DOMAIN}/"
replace_define "$CATALOG_CONFIG" "HTTPS_SERVER" "https://${DOMAIN}/"
replace_define "$CATALOG_CONFIG" "DIR_OPENCART" "${STORE_PATH}/"
replace_define "$CATALOG_CONFIG" "DIR_STORAGE" "${STORE_PATH}/system/storage/"

# Catalog config DIR constants
replace_define "$CATALOG_CONFIG" "DIR_APPLICATION" "${STORE_PATH}/catalog/"
replace_define "$CATALOG_CONFIG" "DIR_EXTENSION" "${STORE_PATH}/extension/"
replace_define "$CATALOG_CONFIG" "DIR_IMAGE" "${STORE_PATH}/image/"
replace_define "$CATALOG_CONFIG" "DIR_SYSTEM" "${STORE_PATH}/system/"
replace_define "$CATALOG_CONFIG" "DIR_CATALOG" "${STORE_PATH}/catalog/"
replace_define "$CATALOG_CONFIG" "DIR_LANGUAGE" "${STORE_PATH}/catalog/language/"
replace_define "$CATALOG_CONFIG" "DIR_TEMPLATE" "${STORE_PATH}/catalog/view/template/"
replace_define "$CATALOG_CONFIG" "DIR_CONFIG" "${STORE_PATH}/system/config/"
replace_define "$CATALOG_CONFIG" "DIR_CACHE" "${STORE_PATH}/system/storage/cache/"
replace_define "$CATALOG_CONFIG" "DIR_DOWNLOAD" "${STORE_PATH}/system/storage/download/"
replace_define "$CATALOG_CONFIG" "DIR_LOGS" "${STORE_PATH}/system/storage/logs/"
replace_define "$CATALOG_CONFIG" "DIR_SESSION" "${STORE_PATH}/system/storage/session/"
replace_define "$CATALOG_CONFIG" "DIR_UPLOAD" "${STORE_PATH}/system/storage/upload/"
replace_define "$CATALOG_CONFIG" "DIR_MODIFICATION" "${STORE_PATH}/system/storage/modification/"

# Admin config DB and URL constants
replace_define "$ADMIN_CONFIG" "DB_DATABASE" "$DB_NAME"
replace_define "$ADMIN_CONFIG" "DB_USERNAME" "$DB_USER"
replace_define "$ADMIN_CONFIG" "DB_PASSWORD" "$DB_PASS"
replace_define "$ADMIN_CONFIG" "HTTP_SERVER" "https://${DOMAIN}/admin/"
replace_define "$ADMIN_CONFIG" "HTTPS_SERVER" "https://${DOMAIN}/admin/"
replace_define "$ADMIN_CONFIG" "HTTP_CATALOG" "https://${DOMAIN}/"
replace_define "$ADMIN_CONFIG" "HTTPS_CATALOG" "https://${DOMAIN}/"
replace_define "$ADMIN_CONFIG" "DIR_OPENCART" "${STORE_PATH}/"
replace_define "$ADMIN_CONFIG" "DIR_STORAGE" "${STORE_PATH}/system/storage/"
replace_define "$ADMIN_CONFIG" "DIR_CATALOG" "${STORE_PATH}/catalog/"

# Admin config DIR constants
replace_define "$ADMIN_CONFIG" "DIR_APPLICATION" "${STORE_PATH}/admin/"
replace_define "$ADMIN_CONFIG" "DIR_EXTENSION" "${STORE_PATH}/extension/"
replace_define "$ADMIN_CONFIG" "DIR_IMAGE" "${STORE_PATH}/image/"
replace_define "$ADMIN_CONFIG" "DIR_SYSTEM" "${STORE_PATH}/system/"
replace_define "$ADMIN_CONFIG" "DIR_LANGUAGE" "${STORE_PATH}/admin/language/"
replace_define "$ADMIN_CONFIG" "DIR_TEMPLATE" "${STORE_PATH}/admin/view/template/"
replace_define "$ADMIN_CONFIG" "DIR_CONFIG" "${STORE_PATH}/system/config/"
replace_define "$ADMIN_CONFIG" "DIR_CACHE" "${STORE_PATH}/system/storage/cache/"
replace_define "$ADMIN_CONFIG" "DIR_DOWNLOAD" "${STORE_PATH}/system/storage/download/"
replace_define "$ADMIN_CONFIG" "DIR_LOGS" "${STORE_PATH}/system/storage/logs/"
replace_define "$ADMIN_CONFIG" "DIR_SESSION" "${STORE_PATH}/system/storage/session/"
replace_define "$ADMIN_CONFIG" "DIR_UPLOAD" "${STORE_PATH}/system/storage/upload/"
replace_define "$ADMIN_CONFIG" "DIR_MODIFICATION" "${STORE_PATH}/system/storage/modification/"

chown -R www-data:www-data "$STORE_PATH"

case "$TIER" in
  base) LIMIT=100 ;;
  pro) LIMIT=1500 ;;
  *) LIMIT=100 ;;
esac

$MYSQL "$DB_NAME" -e "
INSERT INTO \\`oc_setting\\` (store_id, code, \\`key\\`, value, serialized)
VALUES (0, 'novakur_waas', 'novakur_product_limit', '${LIMIT}', 0)
ON DUPLICATE KEY UPDATE value = '${LIMIT}';
"

NGINX_CONF="/etc/nginx/sites-available/nk_${CLIENT_CODE}.conf"
cat > "$NGINX_CONF" <<NGINX
server {
    listen 80;
    server_name ${DOMAIN} www.${DOMAIN};
    root ${STORE_PATH};
    index index.php;
    location / { try_files \$uri \$uri/ /index.php?\$args; }
    location ~ \\.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
    }
    location ~* \\.(js|css|png|jpg|jpeg|gif|ico|svg|woff2)$ {
        expires 30d; add_header Cache-Control public;
    }
}
NGINX

ln -s "$NGINX_CONF" "/etc/nginx/sites-enabled/nk_${CLIENT_CODE}.conf"

nginx -t && nginx -s reload
certbot --nginx -d "$DOMAIN" --non-interactive --agree-tos -m "$EMAIL"

cat > "${STORE_PATH}/novakur_meta.json" <<JSON
{
  "client_code": "${CLIENT_CODE}",
  "domain": "${DOMAIN}",
  "email": "${EMAIL}",
  "tier": "${TIER}",
  "db_name": "${DB_NAME}",
  "db_user": "${DB_USER}",
  "provisioned_at": "$(date -Iseconds)"
}
JSON

echo "PROVISIONED client=${CLIENT_CODE} domain=${DOMAIN}"
