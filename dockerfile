FROM php:8.3-cli

WORKDIR /app

# Install system dependencies yang dibutuhkan composer & Laravel
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql

# Install Composer dari official image (cara paling bersih)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy composer files dulu (terpisah dari kode) supaya layer cache lebih efisien.
# Kalau kode berubah tapi composer.json tidak, Docker tidak perlu install ulang dependency.
COPY composer.json composer.lock ./
RUN composer install --no-scripts --no-autoloader --no-interaction --no-dev

# Baru copy semua kode
COPY . .

# Generate optimized autoloader
RUN composer dump-autoload --optimize

# Buat entrypoint untuk handle migration Laravel
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 8000

ENTRYPOINT ["docker-entrypoint.sh"]