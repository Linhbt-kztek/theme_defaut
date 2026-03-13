#!/usr/bin/env sh

# Chờ netcat tìm cổng 3306 mới run

# Nếu như chạy mysql mở chú thích từ dòng 6 -> 9
# while ! nc -z mysql 3306; do
#     echo "Waiting for MySQL to start..."
#     sleep 1
# done

# # Tiếp tục với các bước tiếp theo
# echo "MySQL container started. Proceeding with entrypoint script..."


composer update --ignore-platform-req=ext-bcmath --ignore-platform-req=ext-sockets

php artisan migrate

chown -R www-data:www-data storage bootstrap/cache webpack.mix.js

php artisan passport:install

php artisan optimize:clear

php artisan set:permisionServer

php artisan create:admin

php artisan create:bucket

# nohup php artisan queue:work --daemon --sleep=3 --timeout=90 > storage/logs/queue.log 2>&1 &

php-fpm -D

/usr/sbin/nginx -g 'daemon off;'