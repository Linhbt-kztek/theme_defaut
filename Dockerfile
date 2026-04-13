# =========================
# Stage 1: Build vendor
# =========================
FROM shinsenter/php:8.2-fpm-nginx-alpine AS vendor

WORKDIR /var/www/html

# Composer cần git + unzip
RUN apk add --no-cache git unzip

COPY composer.json composer.lock ./

# Chỉ cài vendor
RUN composer install \
    --prefer-dist \
    --no-dev \
    --no-interaction \
    --no-scripts \
    --no-autoloader \
    --ignore-platform-req=ext-bcmath \
    --ignore-platform-req=ext-sockets \
    --ignore-platform-req=ext-sodium


# =========================
# Stage 2: Runtime
# =========================
FROM shinsenter/php:8.2-fpm-nginx-alpine

WORKDIR /var/www/html

# config files
COPY entrypoint.sh /usr/local/bin/
COPY ./docker/php/php.ini /usr/local/etc/php/
COPY ./docker/nginx/nginx.conf /etc/nginx/
COPY ./docker/nginx/conf.d/default.conf /etc/nginx/conf.d/
COPY ./docker/nginx/cronfile /etc/crontabs/root

RUN chmod +x /usr/local/bin/entrypoint.sh

# copy source code
COPY . .

# copy vendor từ stage 1
COPY --from=vendor /var/www/html/vendor ./vendor

# generate autoload sau khi có source
RUN composer dump-autoload --optimize

RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

ENTRYPOINT ["entrypoint.sh"]