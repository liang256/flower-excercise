FROM  php:7.3-fpm-alpine3.11

#基礎包 pecl 需要 autoconf , g++ , make
#  --no-cache选项允许不在本地缓存索引，这对于保持容器较小非常有用
# add 時加上 –virtual，是暫時性的為這次加的 package 納入群組。之後若不需要這些 package，就可以在 del 時，指定這個名稱，就可以移除前次加入的 packages。
RUN apk update \
    && apk add --no-cache --virtual .build-deps  curl-dev \
    autoconf \
    g++ \
    make

RUN apk add --no-cache \
        supervisor \
        nodejs \
        npm \
        nginx \
        libzip-dev \
        zip \
        libjpeg-turbo-dev \
        libpng\
        libpng-dev\
        freetype-dev  \
        libxml2-dev \
        zlib


RUN pecl install \
        redis \
        swoole \
    && docker-php-ext-enable \
        redis \
        swoole

RUN docker-php-ext-install \
        bcmath \
        pdo_mysql \
        zip \
        iconv

RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install gd

RUN curl -s https://getcomposer.org/installer | php -- --quiet --install-dir=/usr/bin --filename=composer \
    && composer global require "laravel/envoy" \
    && chmod +x ~/.composer/vendor/bin/envoy && ln -s ~/.composer/vendor/bin/envoy /usr/bin/envoy



WORKDIR /var/www