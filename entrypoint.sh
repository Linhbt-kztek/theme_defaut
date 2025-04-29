#!/usr/bin/env sh

# Chờ netcat tìm cổng 3306 mới run

#Nếu như chạy mysql mở chú thích từ dòng 6 -> 9
while ! nc -z mysql 3306; do
    echo "Waiting for MySQL to start..."
    sleep 1
done

# Tiếp tục với các bước tiếp theo
echo "MySQL container started. Proceeding with entrypoint script..."

composer update

php artisan migrate

php artisan passport:install

chmod -R 777 storage

php-fpm -D
/usr/sbin/nginx -g 'daemon off;'
