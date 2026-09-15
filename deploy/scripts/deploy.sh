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
php artisan migrate --force
php artisan db:seed --class=TextoLegalSeeder --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan queue:restart

php artisan up
trap - EXIT
