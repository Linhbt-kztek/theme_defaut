FROM shinsenter/php:8.2-fpm-nginx-alpine
# FROM php:8.2-fpm-alpine
COPY entrypoint.sh /usr/local/bin/
COPY ./docker/php/php.ini /usr/local/etc/php/
COPY ./docker/nginx/nginx.conf /etc/nginx/
COPY ./docker/nginx/conf.d/default.conf /etc/nginx/conf.d/
#COPY ./docker/php/php-fpm/www.conf /usr/local/etc/php-fpm.d/www.conf.default

# RUN apk add npm && \
#     chmod +x /usr/local/bin/entrypoint.sh

RUN chmod +x /usr/local/bin/entrypoint.sh

COPY . .

EXPOSE 80

ENTRYPOINT ["entrypoint.sh"]