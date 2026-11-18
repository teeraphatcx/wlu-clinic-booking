#!/usr/bin/env sh
set -eu
BACKUP_DIR="${BACKUP_DIR:-./backups}"
mkdir -p "$BACKUP_DIR"
STAMP=$(date +%Y%m%d_%H%M%S)
FILE="$BACKUP_DIR/clinic_booking_$STAMP.sql.gz"
docker compose -f docker-compose.prod.yml exec -T mysql mysqldump -u root -p"$DB_ROOT_PASSWORD" --single-transaction --routines --triggers "$DB_DATABASE" | gzip > "$FILE"
find "$BACKUP_DIR" -type f -name 'clinic_booking_*.sql.gz' -mtime +14 -delete
echo "สร้าง backup: $FILE"
