FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    default-mysql-client \
    && rm -rf /var/lib/apt/lists/*

# Configure and install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip

# Install Redis extension
RUN pecl install redis \
    && docker-php-ext-enable redis

# Allow concurrent local requests to proceed without queueing behind the
# default PHP-FPM pool limit of five workers.
RUN sed -i \
    -e 's/^pm.max_children = 5$/pm.max_children = 12/' \
    -e 's/^pm.start_servers = 2$/pm.start_servers = 4/' \
    -e 's/^pm.min_spare_servers = 1$/pm.min_spare_servers = 2/' \
    -e 's/^pm.max_spare_servers = 3$/pm.max_spare_servers = 6/' \
    /usr/local/etc/php-fpm.d/www.conf

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Expose PHP-FPM port
EXPOSE 9000

CMD ["php-fpm"]

