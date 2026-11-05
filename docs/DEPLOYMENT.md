# InsightFlow Deployment & Operations Guide

## 1. System Requirements

- **Web Server**: Apache 2.4+ (with `mod_rewrite`) or Nginx 1.20+
- **PHP**: PHP 8.2+ with extensions: `pdo`, `pdo_mysql`, `pdo_sqlite`, `json`, `mbstring`, `fileinfo`
- **Database**: MySQL 8.0+ or SQLite 3
- **Analytics Subsystem**: Python 3.11+
- **Memory**: Minimum 2 GB RAM (4 GB recommended for production ETL)
- **Disk**: 10 GB free storage for database & temporary staging

---

## 2. Environment Setup

Copy `.env.example` to `.env`:

```bash
cp .env.example .env
```

Configure your parameters:

```env
APP_NAME="InsightFlow"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://insightflow.yourdomain.com
APP_KEY=base64:7vF8t1mK9yZ0qL3aB2cE4gH5jM6nP8rT9uV1wX2yZ3A=

# Database (MySQL 8+ for production)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=insightflow
DB_USERNAME=insightflow_user
DB_PASSWORD=StrongProductionPassword123!

# Python Analytics Engine Path
PYTHON_PATH=/usr/bin/python3
```

---

## 3. Database Migration & Initial Seeding

Run the schema migration script:

```bash
php database/migrate.php
```

Seed the database with initial users and initial baseline transactions:

```bash
php database/seed.php
```

Trigger initial analytical computations:

```bash
python3 python/main.py run-all
```

---

## 4. Web Server Configuration

### Nginx Virtual Host (`/etc/nginx/sites-available/insightflow.conf`)

```nginx
server {
    listen 80;
    server_name insightflow.yourdomain.com;
    root /var/www/insightflow/public;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT $realpath_root;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Apache `.htaccess` (`public/.htaccess`)

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^ index.php [L]
</IfModule>
```

---

## 5. Automated Analytics Cron Job

To recalculate RFM segments, update cohorts, refresh Pareto curves, and trigger anomaly alerts periodically, add to crontab (`crontab -e`):

```cron
# Recompute InsightFlow analytics every night at 2:00 AM
0 2 * * * /usr/bin/python3 /var/www/insightflow/python/main.py run-all >> /var/www/insightflow/storage/logs/cron.log 2>&1
```

---

## 6. Permissions

Ensure the web server user has write permissions to storage:

```bash
chown -R www-data:www-data /var/www/insightflow/storage
chown -R www-data:www-data /var/www/insightflow/public/uploads
chmod -R 775 /var/www/insightflow/storage
```

