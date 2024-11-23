# Используем базовый образ PHP CLI
FROM php:8.4.1-cli-alpine3.20

# Устанавливаем переменные окружения
ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_HOME=/composer \
    PATH="/composer/vendor/bin:$PATH"

# поиск конфига php --ini
COPY php.ini /usr/local/etc/php

# Устанавливаем зависимости
RUN apk update && apk add --no-cache \
    bash \
    curl \
    unzip \
    icu-libs \
    libzip \
    libpng \
    libjpeg-turbo \
    libxslt \
    zlib \
    busybox-suid \
    supervisor

# Устанавливаем Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Устанавливаем Symfony CLI
RUN curl -sS https://get.symfony.com/cli/installer | bash \
    && mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

# Настраиваем cron
RUN mkdir -p /var/log/cron && touch /var/log/cron/cron.log

# Настраиваем Supervisor
COPY supervisord.conf /etc/supervisord.conf

# Устанавливаем рабочую директорию
WORKDIR /home/app

# Основная команда запускает Supervisor для управления процессами PHP-FPM и cron
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
