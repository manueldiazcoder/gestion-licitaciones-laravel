#!/bin/bash
# ============================================================
# entrypoint.sh — Entrypoint personalizado para DevContainer
#
# Corre automáticamente cada vez que el container inicia.
# Garantiza que storage/ y bootstrap/cache/ tengan permisos
# correctos para que Laravel pueda escribir (logs, cache,
# sesiones, vistas compiladas).
# ============================================================

set -e

# Fix de permisos: el volumen monta los directorios del host
# y los permisos del Dockerfile se pierden.
chown -R www-data:www-data /var/www/html/storage 2>/dev/null || true
chown -R www-data:www-data /var/www/html/bootstrap/cache 2>/dev/null || true
chmod -R 775 /var/www/html/storage 2>/dev/null || true
chmod -R 775 /var/www/html/bootstrap/cache 2>/dev/null || true

# Crear subdirectorios si no existen (por si el volumen está vacío)
mkdir -p /var/www/html/storage/framework/views \
    /var/www/html/storage/framework/cache \
    /var/www/html/storage/framework/sessions \
    /var/www/html/storage/logs \
    /var/www/html/bootstrap/cache

# Symlink public/storage → storage/app/public
if [ ! -L /var/www/html/public/storage ] && [ ! -d /var/www/html/public/storage ]; then
    ln -sf /var/www/html/storage/app/public /var/www/html/public/storage 2>/dev/null || true
fi

# Ejecutar el entrypoint original de la imagen php:8.2-apache
exec docker-php-entrypoint "$@"
