# From official php image.
FROM php:8.3-fpm-alpine
# Install redis driver.
RUN mkdir -p /usr/src/php/ext/redis; \
    curl -fsSL --ipv4 https://github.com/phpredis/phpredis/archive/6.0.2.tar.gz | tar xvz -C "/usr/src/php/ext/redis" --strip 1; \
    docker-php-ext-install redis
# Install dependencies to build MongoDB extension
RUN apk add --no-cache autoconf g++ make openssl-dev
# Install MySQL PDO driver
RUN docker-php-ext-install pdo pdo_mysql