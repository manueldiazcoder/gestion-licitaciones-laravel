FROM php:8.2-apache

# ============================================================
# CONFIGURACIÓN DE APACHE
# ============================================================
RUN a2enmod rewrite && \
    sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf && \
    sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf && \
    sed -i 's|<Directory /var/www/html>|<Directory /var/www/html/public>|' /etc/apache2/apache2.conf && \
    echo "ServerName localhost" >> /etc/apache2/apache2.conf

# ============================================================
# EXTENSIONES PHP (las que necesita Laravel)
# ============================================================
RUN apt-get update && apt-get install -y --no-install-recommends \
        libzip-dev \
        libpng-dev \
        libonig-dev \
        libxml2-dev \
        libicu-dev \
        unzip \
        git \
        curl \
    && docker-php-ext-install \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# ============================================================
# COMPOSER (gestor de dependencias PHP)
# ============================================================
COPY --from=composer:2.7 /usr/bin/composer /usr/local/bin/composer

# ============================================================
# NODE.JS + NPM (para Vite / Laravel Mix)
# ============================================================
RUN apt-get update && apt-get install -y --no-install-recommends nodejs npm && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# ============================================================
# CREAR DIRECTORIOS NECESARIOS (permisos para storage y bootstrap/cache)
# ============================================================
RUN mkdir -p storage/logs storage/framework/sessions storage/framework/views \
             storage/framework/cache bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache && \
    chown -R www-data:www-data storage bootstrap/cache

# ============================================================
# PERMISOS POR DEFECTO DEL WWW-DATA
# ============================================================
RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

# ============================================================
# ENTRYPOINT PERSONALIZADO
# Arregla permisos en storage/ y bootstrap/cache/ en cada inicio
# del container (no solo en build), indispensable porque el
# volumen monta los directorios del host pisando los permisos.
# ============================================================
COPY .devcontainer/entrypoint.sh /usr/local/bin/custom-entrypoint
RUN chmod +x /usr/local/bin/custom-entrypoint
ENTRYPOINT ["custom-entrypoint"]
CMD ["apache2-foreground"]
