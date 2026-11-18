#!/usr/bin/env sh
set -eu
FILE="${1:?กรุณาระบุไฟล์ backup .sql หรือ .sql.gz}"
case "$FILE" in
  *.gz) gzip -dc "$FILE" | docker compose -f docker-compose.prod.yml exec -T mysql mysql -u root -p"$DB_ROOT_PASSWORD" "$DB_DATABASE" ;;
  *) docker compose -f docker-compose.prod.yml exec -T mysql mysql -u root -p"$DB_ROOT_PASSWORD" "$DB_DATABASE" < "$FILE" ;;
esac
echo "กู้คืนฐานข้อมูลเรียบร้อย"
