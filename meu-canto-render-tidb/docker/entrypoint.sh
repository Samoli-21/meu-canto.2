#!/bin/sh
set -eu

cd /var/www/html

PORT="${PORT:-10000}"
case "$PORT" in
  ''|*[!0-9]*) echo '[Meu Canto] PORT inválida.' >&2; exit 1 ;;
esac
sed -ri "s/^Listen 80$/Listen ${PORT}/" /etc/apache2/ports.conf
sed -ri "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

echo '[Meu Canto] A verificar as três entidades da base de dados...'
php database/migrate.php
php database/create-admin.php

echo '[Meu Canto] A iniciar Apache...'
exec apache2-foreground
