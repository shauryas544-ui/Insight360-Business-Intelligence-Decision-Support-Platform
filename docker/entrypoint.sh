#!/bin/bash
set -e

PORT="${PORT:-80}"
sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:${PORT}>/" /etc/apache2/sites-available/000-default.conf

# Initialize .env from .env.example if missing
if [ ! -f /var/www/html/.env ]; then
    cp /var/www/html/.env.example /var/www/html/.env
    echo "PYTHON_PATH=/usr/bin/python3" >> /var/www/html/.env
fi

# Ensure storage directories and permissions
mkdir -p /var/www/html/storage/database \
         /var/www/html/storage/logs \
         /var/www/html/storage/reports \
         /var/www/html/storage/temporary \
         /var/www/html/public/uploads

chown -R www-data:www-data /var/www/html/storage /var/www/html/public/uploads
chmod -R 775 /var/www/html/storage /var/www/html/public/uploads

# Run migrations and seeding if SQLite DB does not exist
if [ ! -f /var/www/html/storage/database/database.sqlite ]; then
    echo "[INFO] Running initial database migrations..."
    php /var/www/html/database/migrate.php
    echo "[INFO] Seeding realistic sample dataset..."
    php /var/www/html/database/seed.php
    echo "[INFO] Running Python analytics engine..."
    python3 /var/www/html/python/main.py run-all || true
    chown -R www-data:www-data /var/www/html/storage/database
fi

echo "[INFO] Starting Apache on port ${PORT}..."
exec apache2-foreground
