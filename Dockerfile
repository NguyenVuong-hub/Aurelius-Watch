# 1. Gọi hệ điều hành chứa máy chủ web Apache gốc và PHP 8.2
FROM php:8.2-apache

# 2. Ép hệ thống cài đặt trực tiếp, vĩnh viễn các bộ đọc MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# 3. Copy toàn bộ mã nguồn của bạn vào đúng ổ chứa web của Apache
COPY . /var/www/html/

# 4. Cấp quyền đọc/ghi file tuyệt đối cho máy chủ
RUN chown -R www-data:www-data /var/www/html/
