#!/bin/bash
set -Eeuo pipefail

APP_DIR="/var/www/aureuserp"
cd "$APP_DIR"

log() { echo "[aureus-runtime-install] $(date '+%Y-%m-%d %H:%M:%S') $*"; }
extract_app_key() {
    grep '^APP_KEY=' .env \
        | tail -n 1 \
        | grep -Eo 'base64:[A-Za-z0-9+\/=]+' \
        | head -n 1 \
        || true
}

DB_HOST="${DB_HOST:-127.0.0.1}"
DB_PORT="${DB_PORT:-3306}"
DB_DATABASE="${DB_DATABASE:-aureuserp}"
DB_USERNAME="${DB_USERNAME:-aureus}"
DB_PASSWORD="${DB_PASSWORD:-aureus123}"

ADMIN_NAME="${ADMIN_NAME:-Administrator}"
ADMIN_EMAIL="${ADMIN_EMAIL:-admin@example.com}"
ADMIN_PASSWORD="${ADMIN_PASSWORD:-password}"

use_internal_mysql() { [[ "$DB_HOST" == "127.0.0.1" || "$DB_HOST" == "localhost" ]]; }

if [ -z "${ADMIN_NAME// }" ]; then
    ADMIN_NAME="Administrator"
fi

if ! [[ "$ADMIN_EMAIL" =~ ^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$ ]]; then
    ADMIN_EMAIL="admin@example.com"
fi

if [ "${#ADMIN_PASSWORD}" -lt 8 ]; then
    ADMIN_PASSWORD="password"
fi

if use_internal_mysql; then
    log "Preparing internal MySQL..."
    mkdir -p /run/mysqld /var/lib/mysql
    chown -R mysql:mysql /run/mysqld /var/lib/mysql

    if [ ! -d /var/lib/mysql/mysql ]; then
        log "Initializing MySQL data directory..."
        mysqld --initialize-insecure --user=mysql --datadir=/var/lib/mysql
    fi

    log "Starting temporary MySQL for installation..."
    mysqld --user=mysql --datadir=/var/lib/mysql &
    MYSQL_PID=$!

    for i in $(seq 1 120); do
        if mysqladmin --silent ping 2>/dev/null; then
            log "Internal MySQL is ready."
            break
        fi
        if [ "$i" -eq 120 ]; then
            log "ERROR: Internal MySQL did not become ready."
            exit 1
        fi
        sleep 1
    done

    log "Ensuring internal database/user exist..."
    mysql -u root <<SQL
CREATE DATABASE IF NOT EXISTS \`${DB_DATABASE}\`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '${DB_USERNAME}'@'127.0.0.1'
    IDENTIFIED WITH caching_sha2_password BY '${DB_PASSWORD}';
CREATE USER IF NOT EXISTS '${DB_USERNAME}'@'localhost'
    IDENTIFIED WITH caching_sha2_password BY '${DB_PASSWORD}';
GRANT ALL PRIVILEGES ON \`${DB_DATABASE}\`.* TO '${DB_USERNAME}'@'127.0.0.1';
GRANT ALL PRIVILEGES ON \`${DB_DATABASE}\`.* TO '${DB_USERNAME}'@'localhost';
FLUSH PRIVILEGES;
SQL
else
    log "Waiting for external MySQL at ${DB_HOST}:${DB_PORT}..."
    for i in $(seq 1 120); do
        if php -r "try { new PDO('mysql:host=${DB_HOST};port=${DB_PORT};dbname=${DB_DATABASE}', '${DB_USERNAME}', '${DB_PASSWORD}'); } catch (Throwable \$e) { exit(1); }" 2>/dev/null; then
            log "External MySQL is reachable."
            break
        fi
        if [ "$i" -eq 120 ]; then
            log "ERROR: cannot reach external MySQL."
            exit 1
        fi
        sleep 1
    done
fi

log "Ensuring Laravel cache/storage paths exist..."
mkdir -p \
    storage/framework/views \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/logs \
    bootstrap/cache

if [ -z "${APP_KEY:-}" ]; then
    APP_KEY="$(extract_app_key)"
fi

if [ -z "${APP_KEY:-}" ]; then
    log "APP_KEY not provided; generating one..."
    php artisan key:generate --force --no-interaction || true
    APP_KEY="$(extract_app_key)"
fi

if [ -n "${APP_KEY:-}" ]; then
    sed -i '/^APP_KEY=/d' .env
    printf 'APP_KEY=%s\n' "${APP_KEY}" >> .env
fi

installed_marker=false
if [ -f storage/installed ]; then
    installed_marker=true
fi

users_count=0
if php -r "try { \$pdo = new PDO('mysql:host=${DB_HOST};port=${DB_PORT};dbname=${DB_DATABASE}', '${DB_USERNAME}', '${DB_PASSWORD}'); \$stmt = \$pdo->query(\"SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '${DB_DATABASE}' AND table_name = 'users'\"); \$exists = (int)\$stmt->fetchColumn(); if (!\$exists) { echo 0; exit(0);} \$count = (int)\$pdo->query(\"SELECT COUNT(*) FROM users\")->fetchColumn(); echo \$count; } catch (Throwable \$e) { echo 0; }" > /tmp/users_count.txt; then
    users_count="$(cat /tmp/users_count.txt 2>/dev/null || echo 0)"
fi

if [ "$installed_marker" = true ] || [ "${users_count:-0}" -gt 0 ]; then
    log "ERP installation already present; skipping erp:install and module installers."
else
    log "Running first-time ERP installation..."
    timeout 1200 php artisan erp:install --force --no-interaction \
        --admin-name="$ADMIN_NAME" \
        --admin-email="$ADMIN_EMAIL" \
        --admin-password="$ADMIN_PASSWORD"

    log "Installing all installable modules..."
    MODULES_TO_INSTALL="$(
        grep -R --include='*ServiceProvider.php' -l 'hasInstallCommand' plugins/webkul \
            | xargs -r sed -n "s/.*public static string \$name = '\([^']*\)'.*/\1/p" \
            | sort -u
    )"

    for module in $MODULES_TO_INSTALL; do
        log "Installing module: ${module}"
        timeout 300 php artisan "${module}:install" --no-interaction || log "WARNING: module ${module} failed or timed out."
    done
fi

log "Refreshing package discovery and static assets..."
timeout 180 php artisan package:discover --ansi || log "WARNING: package:discover failed."
timeout 180 php artisan filament:assets || log "WARNING: filament:assets failed."
timeout 180 php artisan vendor:publish --tag=laravel-assets --force --ansi || log "WARNING: vendor:publish failed."

if use_internal_mysql; then
    log "Stopping temporary MySQL..."
    mysqladmin -u root shutdown || true
    wait "${MYSQL_PID:-0}" 2>/dev/null || true
    chown -R mysql:mysql /var/lib/mysql
fi

log "Runtime install checks complete."
