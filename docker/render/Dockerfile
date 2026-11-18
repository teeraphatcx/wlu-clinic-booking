FROM node:22-alpine AS assets

WORKDIR /app

COPY package*.json ./
RUN npm install --no-audit --no-fund

COPY resources ./resources
COPY public ./public
COPY vite.config.js ./
RUN npm run build

FROM php:8.3-fpm-bookworm

ENV COMPOSER_ALLOW_SUPERUSER=1 \
    APP_ENV=production \
    APP_DEBUG=false

RUN apt-get update && apt-get install -y --no-install-recommends \
        default-mysql-client \
        gettext-base \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libonig-dev \
        libpng-dev \
        libpq-dev \
        libxml2-dev \
        libzip-dev \
        nginx \
        supervisor \
        unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        bcmath \
        exif \
        gd \
        mbstring \
        pcntl \
        pdo_mysql \
        pdo_pgsql \
        zip \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && rm -rf /var/lib/apt/lists/*

RUN sed -i \
    -e 's/^pm.max_children = 5$/pm.max_children = 12/' \
    -e 's/^pm.start_servers = 2$/pm.start_servers = 4/' \
    -e 's/^pm.min_spare_servers = 1$/pm.min_spare_servers = 2/' \
    -e 's/^pm.max_spare_servers = 3$/pm.max_spare_servers = 6/' \
    /usr/local/etc/php-fpm.d/www.conf

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

COPY . .
RUN rm -f bootstrap/cache/*.php \
    && mkdir -p \
        bootstrap/cache \
        storage/app/public \
        storage/framework/cache \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
    && chown -R www-data:www-data bootstrap/cache storage

COPY --from=assets /app/public/build ./public/build
COPY docker/render/nginx.conf.template /etc/nginx/templates/default.conf.template
COPY docker/render/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/render/entrypoint.sh /usr/local/bin/render-entrypoint

RUN chmod +x /usr/local/bin/render-entrypoint \
    && rm -f /etc/nginx/sites-enabled/default

EXPOSE 10000

ENTRYPOINT ["render-entrypoint"]
CMD ["supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
