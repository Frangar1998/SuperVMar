#!/bin/sh
set -e

if [ ! -f /var/www/html/api-vmar/vendor/autoload.php ]; then
    echo "[entrypoint] vendor vacío, ejecutando composer install..."
    cd /var/www/html/api-vmar
    composer install --no-interaction --optimize-autoloader
    echo "[entrypoint] composer install completado."
fi

echo "[entrypoint] Calentando caché de Symfony..."
cd /var/www/html/api-vmar
php bin/console cache:clear --no-debug 2>/dev/null || true
php bin/console cache:warmup --no-debug 2>/dev/null || true
echo "[entrypoint] Caché lista."

exec docker-php-entrypoint "$@"
