#!/usr/bin/env bash
# ---------------------------------------------------------------------
# Aquafast Treina - setup do ambiente local
# Referencia para a etapa 0. Leia antes de rodar; nao e para executar
# as cegas.
# ---------------------------------------------------------------------
set -euo pipefail

echo "==> 1. Projeto Laravel"
composer create-project laravel/laravel .

echo "==> 2. Ambiente"
cp .env.example .env
php artisan key:generate
# Ajuste DB_*, MAIL_* e YOUTUBE_API_KEY no .env antes de seguir.

echo "==> 3. Dependencias de producao"
composer require filament/filament:"^4.0"
composer require laravel/fortify
composer require spatie/laravel-permission
composer require spatie/laravel-activitylog
composer require maatwebsite/excel
composer require guzzlehttp/guzzle

echo "==> 4. Dependencias de desenvolvimento"
composer require --dev pestphp/pest pestphp/pest-plugin-laravel
composer require --dev laravel/pint
composer require --dev barryvdh/laravel-debugbar

echo "==> 5. Publicacao de configuracoes"
php artisan vendor:publish --provider="Laravel\Fortify\FortifyServiceProvider"
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-migrations"

echo "==> 6. Painel admin"
php artisan filament:install --panels
# Quando perguntar o id do painel, responda: admin

echo "==> 7. Front-end"
npm install
npm install -D tailwindcss @tailwindcss/vite
npm install sortablejs

echo "==> 8. Pest"
php artisan pest:install

echo "==> 9. Disco privado de materiais"
# Acrescente em config/filesystems.php:
#
#   'materiais' => [
#       'driver' => 'local',
#       'root'   => storage_path('app/materiais'),
#       'throw'  => false,
#   ],
#
mkdir -p storage/app/materiais
echo "*" > storage/app/materiais/.gitignore
echo "!.gitignore" >> storage/app/materiais/.gitignore

echo "==> 10. IMPORTANTE: tabela usuarios"
echo "    Antes de migrar, aplique padroes/04-banco-e-migrations.md secao 2."
echo "    Renomear users -> usuarios exige cinco ajustes. Pular um deles"
echo "    quebra login ou reset de senha."

echo "==> 11. Banco"
php artisan migrate
php artisan db:seed

echo "==> 12. Qualidade"
vendor/bin/pint
php artisan test

echo
echo "Pronto. Suba com: php artisan serve  +  npm run dev"
echo "Em outro terminal, para a fila: php artisan queue:work"
