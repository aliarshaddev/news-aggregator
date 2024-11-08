# From official php image.
FROM php:8.3-cli-alpine
# Install quality-of-life packages.
RUN apk add --no-cache bash curl git vim
# Install composer for php deps.
RUN apk add --no-cache composer
# Composer uses its php binary, but we want it to use the container's one
RUN rm -f /usr/bin/php83
RUN ln -s /usr/local/bin/php /usr/bin/php83
# Install redis driver.
RUN mkdir -p /usr/src/php/ext/redis; \
    curl -fsSL --ipv4 https://github.com/phpredis/phpredis/archive/6.0.2.tar.gz | tar xvz -C "/usr/src/php/ext/redis" --strip 1; \
    docker-php-ext-install redis
# Install nodejs and npm for frontend.
RUN apk add --no-cache nodejs npm
# Install dependencies to build MongoDB extension
RUN apk add --no-cache autoconf g++ make openssl-dev
# Install MySQL PDO driver
RUN docker-php-ext-install pdo pdo_mysql
# Prevent container from exiting early.
CMD ["sleep", "infinity"]
