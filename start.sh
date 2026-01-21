#!/usr/bin/env bash

set -e

echo "🚀 Starting Laravel on Railway..."

php artisan key:generate --force || true

php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

php artisan migrate --force || true

php artisan config:cache

exec php -S 0.0.0.0:${PORT} -t public
