#!/bin/sh
set -eu

export PORT="${PORT:-10000}"

# Render's generateValue produces a base64-encoded secret without Laravel's
# required `base64:` prefix. Normalize it before Laravel boots so encrypted
# sessions and CSRF tokens work on every web request.
if [ -z "${APP_KEY:-}" ]; then
    echo "APP_KEY is required in the Render environment." >&2
    exit 1
fi

case "$APP_KEY" in
    base64:*) ;;
    *) export APP_KEY="base64:$(printf '%s' "$APP_KEY" | php -r 'echo base64_encode(hash("sha256", stream_get_contents(STDIN), true));')" ;;
esac

envsubst '${PORT}' < /etc/nginx/templates/default.conf.template > /etc/nginx/conf.d/default.conf

mkdir -p \
    bootstrap/cache \
    storage/app/public \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs

if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    attempt=1
    until php artisan migrate --force; do
        if [ "$attempt" -ge 30 ]; then
            echo "Database migration failed after 30 attempts." >&2
            exit 1
        fi

        echo "Waiting for the database (attempt $attempt/30)..." >&2
        attempt=$((attempt + 1))
        sleep 2
    done
fi

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link || true

chown -R www-data:www-data bootstrap/cache storage
chmod -R ug+rwX bootstrap/cache storage

exec "$@"
