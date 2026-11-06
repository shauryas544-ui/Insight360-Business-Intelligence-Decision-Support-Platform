FROM php:8.2-apache

# Install system dependencies & Python 3
RUN apt-get update && apt-get install -y --no-install-recommends \
    python3 \
    sqlite3 \
    libsqlite3-dev \
    libzip-dev \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Install required PHP extensions
RUN docker-php-ext-install pdo pdo_sqlite pdo_mysql

# Enable Apache rewrite module
RUN a2enmod rewrite

# Copy custom Apache configuration
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html/

# Copy entrypoint script and make executable
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Expose default HTTP port
EXPOSE 80

# Run entrypoint
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
