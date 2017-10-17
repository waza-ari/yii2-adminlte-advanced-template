FROM php:7.0-fpm

COPY ./php.ini /usr/local/etc/php/php.ini

RUN apt-get update && apt-get install -y gettext && docker-php-ext-install pdo pdo_mysql mysqli gettext \
    && pecl install -o -f xdebug \
    && docker-php-ext-enable xdebug
    #&& rm -rf /tmp/pear \
    #&& echo "zend_extension=/usr/local/lib/php/extensions/no-debug-non-zts-20151012/xdebug.so" > /usr/local/etc/php/conf.d/xdebug.ini \
    #&& DEBIAN_FRONTEND=noninteractive apt-get update \
    #&& DEBIAN_FRONTEND=noninteractive apt-get install -y pdftk