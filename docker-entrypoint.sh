#!/bin/sh
set -e

cd /var/www/html

# Crear symlink si falta (si ya existe, ignorar error)
php artisan storage:link 2>/dev/null || true

# Permisos correctos para Laravel
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

exec "$@"
