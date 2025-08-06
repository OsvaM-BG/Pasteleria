# Imagen base con PHP y Apache
FROM php:8.1-apache

# Instalar extensiones necesarias
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copiar archivos al contenedor
COPY . /var/www/html/

# Dar permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html
