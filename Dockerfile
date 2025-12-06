# Imagen base con PHP 8.2 y Apache
FROM php:8.2-apache

# Instalar extensiones necesarias
RUN docker-php-ext-install pdo pdo_mysql

# Habilitar módulos necesarios
RUN a2enmod rewrite
RUN a2enmod headers   # ← ESTA ES LA LÍNEA QUE FALTABA

# Copiar todo el backend al servidor Apache
COPY . /var/www/html/

# Ajustar permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 80

