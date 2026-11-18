#!/bin/sh
set -eu

if [ "${DB_CONNECTION:-mysql}" = "mysql" ]; then
  echo "รอฐานข้อมูล MySQL..."
  until mysqladmin ping --skip-ssl -h"${DB_HOST:-mysql}" -P"${DB_PORT:-3306}" -u"${DB_USERNAME}" -p"${DB_PASSWORD}" --silent; do sleep 2; done
fi

php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link || true

# The application directory is bind-mounted in development, so Dockerfile
# ownership changes do not apply to storage and bootstrap/cache at runtime.
mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views storage/app/public bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rwX storage bootstrap/cache

exec "$@"
