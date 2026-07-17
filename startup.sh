#!/bin/bash
set -e

echo "==> Running QuillNova startup..."

# Generate app key if not set
if [ -z "$APP_KEY" ]; then
    echo "==> Generating APP_KEY..."
    php artisan key:generate --force
fi

# Clear and cache configs for production
echo "==> Caching configs..."
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
echo "==> Running database migrations..."
php artisan migrate --force

# Run seeders
echo "==> Running database seeders..."
php artisan db:seed --force

echo "==> Starting Laravel server on port $PORT..."
exec php artisan serve --host=0.0.0.0 --port="${PORT:-8000}"
