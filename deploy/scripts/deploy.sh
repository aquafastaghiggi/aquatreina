#!/usr/bin/env bash
set -euo pipefail

APP_DIR="/var/www/aquafast-treina/current"

cd "$APP_DIR"
php artisan down --retry=60
trap 'php artisan up' EXIT

git fetch origin main
git checkout main
git pull --ff-only origin main
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
npm ci
npm run build
php artisan livewire:publish --assets
mkdir -p public/livewire
cp public/vendor/livewire/livewire.min.js public/livewire/livewire.min.js
cp public/vendor/livewire/livewire.min.js.map public/livewire/livewire.min.js.map
php artisan migrate --force
php artisan db:seed --class=TextoLegalSeeder --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan queue:restart

if [ "$(id -u)" -eq 0 ]; then
    chown -R www-data:www-data storage bootstrap/cache public/livewire public/vendor/livewire
fi

php artisan up
trap - EXIT
