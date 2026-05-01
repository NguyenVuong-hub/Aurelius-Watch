FROM php:8.2-apache

# 1. Cài đặt các extension Database
RUN docker-php-ext-install mysqli pdo pdo_mysql

# 2. Xử lý triệt để lỗi xung đột MPM (AH00534)
RUN a2dismod mpm_event mpm_worker || true \
    && a2enmod mpm_prefork

# 3. Cấu hình Dynamic Port: Ép Apache lắng nghe trên biến $PORT của Railway thay vì 80
RUN sed -i "s/Listen 80/Listen \${PORT}/g" /etc/apache2/ports.conf \
    && sed -i "s/:80/:\${PORT}/g" /etc/apache2/sites-available/000-default.conf

# 4. Copy mã nguồn và phân quyền
COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html/
