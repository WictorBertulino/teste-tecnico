#!/bin/sh
set -e

cd /var/www/html

if [ ! -f .env ]; then
  cp .env.example .env
fi

if [ ! -f vendor/autoload.php ]; then
  composer install --no-interaction
fi

if ! grep -q "^APP_KEY=base64:" .env; then
  php artisan key:generate --force
fi

rm -f storage/framework/backend-ready

until php artisan migrate --seed --force; do
  echo "Aguardando banco de dados..."
  sleep 3
done

touch storage/framework/backend-ready

php-fpm
