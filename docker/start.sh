#!/usr/bin/env bash
set -e

: "${PORT:=10000}"

sed "s/\${PORT}/${PORT}/g" /etc/nginx/templates/default.conf.template > /etc/nginx/sites-available/default

php artisan storage:link || true
php artisan config:cache
php artisan route:cache
php artisan view:cache

php-fpm -D
nginx -g "daemon off;"
