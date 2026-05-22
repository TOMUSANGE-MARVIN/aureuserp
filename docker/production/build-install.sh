set -Eeuo pipefail
trap 'echo "[build-install] ERROR on line ${LINENO}: ${BASH_COMMAND}" >&2' ERR

APP_DIR="/var/www/aureuserp"

ADMIN_NAME="${ADMIN_NAME:-Administrator}"
ADMIN_EMAIL="${ADMIN_EMAIL:-admin@example.com}"
ADMIN_PASSWORD="${ADMIN_PASSWORD:-password}"

echo "[build-install] Initialising MySQL data directory..."
mkdir -p /run/mysqld
rm -rf /var/lib/mysql && mkdir -p /var/lib/mysql
chown -R mysql:mysql /run/mysqld /var/lib/mysql
mysqld --initialize-insecure --user=mysql --datadir=/var/lib/mysql

echo "[build-install] Starting MySQL..."
mysqld --user=mysql --datadir=/var/lib/mysql &
MYSQL_PID=$!

echo "[build-install] Waiting for MySQL to be ready..."

for i in $(seq 1 60); do
    if mysqladmin --silent ping 2>/dev/null; then
        echo "[build-install] MySQL is ready."
        break
    fi
    if [ "$i" -eq 60 ]; then
        echo "[build-install] ERROR: MySQL did not start within 60 seconds."
        exit 1
    fi
    sleep 1
done

echo "[build-install] Creating database and user..."
mysql -u root < /docker-entrypoint-initdb.d/init.sql

cd "$APP_DIR"

echo "[build-install] Ensuring Laravel cache/storage paths exist..."
mkdir -p \
    storage/framework/views \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/logs \
    bootstrap/cache

echo "[build-install] Generating application key..."
php artisan key:generate --force --no-interaction

echo "[build-install] Installing AureusERP (migrations, seeders, roles, admin)..."
php artisan erp:install --force --no-interaction \
    --admin-name="$ADMIN_NAME" \
    --admin-email="$ADMIN_EMAIL" \
    --admin-password="$ADMIN_PASSWORD"

echo "[build-install] Installing all installable modules..."
MODULES_TO_INSTALL="$(
    grep -R --include='*ServiceProvider.php' -l 'hasInstallCommand' plugins/webkul \
        | xargs -r sed -n "s/.*public static string \$name = '\([^']*\)'.*/\1/p" \
        | sort -u
)"

INSTALL_ERRORS=0
for module in $MODULES_TO_INSTALL; do
    echo "[build-install] -> Installing ${module}..."
    if timeout 300 php artisan "${module}:install" --no-interaction; then
        echo "[build-install] -> ${module} installed."
    else
        echo "[build-install] -> WARNING: ${module} install failed or timed out (non-fatal, continuing)."
        INSTALL_ERRORS=$((INSTALL_ERRORS + 1))
    fi
done

if [ "$INSTALL_ERRORS" -gt 0 ]; then
    echo "[build-install] WARNING: ${INSTALL_ERRORS} module(s) failed to install. Check logs above."
fi

echo "[build-install] Refreshing package discovery and publishing frontend assets..."
if ! timeout 180 php artisan package:discover --ansi; then
    echo "[build-install] WARNING: package:discover failed or timed out (non-fatal)."
fi

if ! timeout 180 php artisan filament:assets; then
    echo "[build-install] WARNING: filament:assets failed or timed out (non-fatal)."
fi

if ! timeout 180 php artisan vendor:publish --tag=laravel-assets --force --ansi; then
    echo "[build-install] WARNING: vendor:publish for laravel-assets failed or timed out (non-fatal)."
fi

echo "[build-install] Shutting down MySQL..."
mysqladmin -u root shutdown
wait "$MYSQL_PID" 2>/dev/null || true

chown -R mysql:mysql /var/lib/mysql

echo "[build-install] AureusERP installation complete."
