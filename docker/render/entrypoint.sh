#!/bin/sh
set -eu

export PORT="${PORT:-10000}"

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
