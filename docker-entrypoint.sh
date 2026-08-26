#!/bin/bash
set -e

# Render assigns a dynamic port via $PORT — point Apache at it
sed -i "s/80/${PORT:-80}/g" /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf

# Cache config fresh on every boot
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run any pending migrations (safe — skips already-applied ones)
php artisan migrate --force

exec apache2-foreground