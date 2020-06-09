FROM php:7.3-fpm

RUN apt-get update \
    && apt-get install -y libzip-dev zip libcurl4-openssl-dev pkg-config libssl-dev gnupg nginx

RUN set -xe \
    && docker-php-ext-install zip

RUN pecl install apcu \
    && docker-php-ext-enable apcu

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

ADD .docker/php/xdebug.ini /etc/php/conf.d/

RUN curl -sSk https://getcomposer.org/installer | php -- --disable-tls \
    && mv composer.phar /usr/local/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER 1
RUN mkdir -p /var/www/.composer && chown -R www-data /var/www/.composer

COPY .docker/php/ini/conf.d /usr/local/etc/php
COPY apiGateway/ /var/www/apiGateway

RUN PATH=$PATH:/var/www/apiGateway/vendor/bin:bin

WORKDIR /var/www/apiGateway

RUN composer install

EXPOSE 9000

CMD ["php-fpm"]