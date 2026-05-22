#!/bin/bash
set -e

APP_DIR="/var/www/aureuserp"
cd "$APP_DIR"

log() { echo "[aureus-entrypoint] $(date '+%Y-%m-%d %H:%M:%S') $*"; }
extract_app_key() {
    grep '^APP_KEY=' .env \
        | tail -n 1 \
        | sed -E 's/^APP_KEY=(base64:[A-Za-z0-9+\/=]+).*$/\1/' \
        || true
}

DB_HOST="${DB_HOST:-127.0.0.1}"
DB_PORT="${DB_PORT:-3306}"
DB_DATABASE="${DB_DATABASE:-aureus}"
DB_USERNAME="${DB_USERNAME:-aureus}"
DB_PASSWORD="${DB_PASSWORD:-aureus}"

use_internal_mysql() { [[ "$DB_HOST" == "127.0.0.1" || "$DB_HOST" == "localhost" ]]; }

if [ -z "${APP_KEY:-}" ]; then
    unset APP_KEY
    APP_KEY="$(extract_app_key)"
    if [ -z "${APP_KEY:-}" ]; then
        unset APP_KEY
    fi
fi

if use_internal_mysql; then
    log "Mode: INTERNAL MySQL"
    export MYSQL_AUTOSTART=true
else
    log "Mode: EXTERNAL MySQL (${DB_HOST}:${DB_PORT})"
    export MYSQL_AUTOSTART=false
fi

sed_escape() { printf '%s' "$1" | sed -e 's/[\\&|]/\\&/g'; }

set_env() {
    local key="$1" val
    val=$(sed_escape "$2")
    sed -i "s|^${key}=.*|${key}=${val}|" .env
}

log "Applying runtime environment overrides..."
set_env DB_HOST     "$DB_HOST"
set_env DB_PORT     "$DB_PORT"
set_env DB_DATABASE "$DB_DATABASE"
set_env DB_USERNAME "$DB_USERNAME"
set_env DB_PASSWORD "$DB_PASSWORD"

set_env APP_ENV "${APP_ENV:-production}"
set_env APP_DEBUG "${APP_DEBUG:-false}"

[ -n "$APP_URL" ]      && set_env APP_URL      "$APP_URL"
[ -n "$APP_KEY" ]      && set_env APP_KEY      "$APP_KEY"
[ -n "$APP_NAME" ]     && set_env APP_NAME     "\"${APP_NAME}\""
[ -n "$APP_LOCALE" ]   && set_env APP_LOCALE   "$APP_LOCALE"
[ -n "$APP_CURRENCY" ] && set_env APP_CURRENCY "$APP_CURRENCY"
[ -n "$APP_TIMEZONE" ] && set_env APP_TIMEZONE "$APP_TIMEZONE"

# Mail
[ -n "$MAIL_MAILER" ]       && set_env MAIL_MAILER       "$MAIL_MAILER"
[ -n "$MAIL_HOST" ]         && set_env MAIL_HOST         "$MAIL_HOST"
[ -n "$MAIL_PORT" ]         && set_env MAIL_PORT         "$MAIL_PORT"
[ -n "$MAIL_USERNAME" ]     && set_env MAIL_USERNAME     "$MAIL_USERNAME"
[ -n "$MAIL_PASSWORD" ]     && set_env MAIL_PASSWORD     "$MAIL_PASSWORD"
[ -n "$MAIL_ENCRYPTION" ]   && set_env MAIL_ENCRYPTION   "$MAIL_ENCRYPTION"
[ -n "$MAIL_FROM_ADDRESS" ] && set_env MAIL_FROM_ADDRESS "$MAIL_FROM_ADDRESS"
[ -n "$MAIL_FROM_NAME" ]    && set_env MAIL_FROM_NAME    "\"${MAIL_FROM_NAME}\""

log "Running runtime installation checks..."
/usr/local/bin/runtime-install.sh

APP_KEY_FROM_ENV_FILE="$(extract_app_key)"
if [ -n "$APP_KEY_FROM_ENV_FILE" ]; then
    export APP_KEY="$APP_KEY_FROM_ENV_FILE"
fi

log "Refreshing cached configuration..."

php artisan optimize:clear --no-interaction 2>/dev/null || true

log "Starting services via Supervisor..."

exec "$@"
