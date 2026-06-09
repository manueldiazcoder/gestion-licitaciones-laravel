-- ============================================================
-- Inicialización de la base de datos para Laravel
-- Docker Compose ya crea la base 'prueba_laravel' via MYSQL_DATABASE
-- Este script se ejecuta automáticamente al iniciar el contenedor
-- ============================================================

USE prueba_laravel;

-- Forzar UTF-8 para que los datos con acentos se inserten correctamente
SET NAMES utf8mb4;

-- NOTA: Las tablas se crean automáticamente via php artisan migrate
-- Este archivo queda como placeholder para config adicional futura.
