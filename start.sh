#!/usr/bin/env bash

set -e

echo "Starting Laravel on Railway..."

php artisan config:clear || true
php artisan cache:clear || true
php artisan route:clear || true
php artisan view:clear || true

php artisan migrate --force || true

exec php -S 0.0.0.0:${PORT} -t public
