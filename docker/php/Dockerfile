FROM php:8.5-fpm-bookworm

ARG HOST_UID=1000
ARG HOST_GID=1000

ENV DEBIAN_FRONTEND=noninteractive \
    COMPOSER_ALLOW_SUPERUSER=1

RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    gosu \
    msmtp \
    msmtp-mta \
    libfreetype6-dev \
    libicu-dev \
    libonig-dev \
    libsodium-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libwebp-dev \
    libxml2-dev \
    libxslt1-dev \
    libzip-dev \
    unzip \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install \
    bcmath \
    ftp \
    gd \
    intl \
    mbstring \
    pdo_mysql \
    soap \
    sockets \
    sodium \
    xsl \
    zip

RUN pecl install redis \
    && docker-php-ext-enable redis

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY docker/php/msmtprc /etc/msmtprc

RUN { \
        echo 'memory_limit=4G'; \
        echo 'max_execution_time=1800'; \
        echo 'realpath_cache_size=10M'; \
        echo 'realpath_cache_ttl=7200'; \
        echo 'sendmail_path = "/usr/bin/msmtp -t -i"'; \
    } > /usr/local/etc/php/conf.d/zz-magento.ini

RUN groupmod -o -g "${HOST_GID}" www-data \
    && usermod -o -u "${HOST_UID}" -g www-data www-data

WORKDIR /var/www/html

CMD ["php-fpm", "-F"]
