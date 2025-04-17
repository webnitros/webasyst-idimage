ARG PHP_VERSION=8.2
ARG TZ=Europe/Moscow

# Первый этап: установка Composer и копирование файлов composer.json и composer.lock
########################
####### composer
########################
FROM laravelsail/php82-composer:latest as composer
COPY ./composer.json /var/www/html/
COPY ./composer.lock /var/www/html/

####### ////////////// the end


########################
####### base
########################
# Второй этап: установка зависимостей с использованием Composer
FROM webnitros/php-laravel-worker-composer-mysql:latest as base

USER root

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Копирование конфигурационного файла supervisord
COPY ./docker/app/supervisor/supervisord.conf /etc/supervisord.conf
RUN mkdir -p /var/log/supervisor

COPY ./docker/app/php.ini-production /usr/local/etc/php/conf.d/php.ini

# Установка необходимых пакетов для работы с MySQL
RUN apk update && apk add --no-cache mariadb-dev && \
    docker-php-ext-install mysqli pdo_mysql


####### ////////////// the end

########################
####### development
########################
FROM base AS development

####### ////////////// the end

########################
####### deploy
########################
FROM base AS deploy

# копируем composer
COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY --from=composer /var/www/html /var/www/html

# копируем файлы
COPY --chown=www-data:www-data . /var/www/html

USER www-data

# выполняем инсталяцию слоя в контенер
RUN composer install --no-dev
####### ////////////// the end
