#!/bin/sh
set -eu

cd /var/www/html
PORT="${PORT:-10000}"
case "$PORT" in
  ''|*[!0-9]*) echo '[Meu Canto] ERRO: PORT deve ser numérica.' >&2; exit 1 ;;
esac

if [ ! -f /var/www/html/public/index.php ] || [ ! -f /var/www/html/routes/web.php ]; then
  echo '[Meu Canto] ERRO: estrutura MVC incompleta.' >&2
  find /var/www/html -maxdepth 2 -type f | sort >&2 || true
  exit 1
fi

# Render encaminha o tráfego para a variável PORT.
sed -ri "s/^Listen 80$/Listen ${PORT}/" /etc/apache2/ports.conf
sed -ri "s/<VirtualHost \\*:80>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

printf '[Meu Canto] Porta configurada: %s\n' "$PORT"
printf '[Meu Canto] DocumentRoot: %s\n' "$APACHE_DOCUMENT_ROOT"
apache2ctl -t

# As migrações são idempotentes e criam apenas as três tabelas do projecto.
echo '[Meu Canto] A verificar a base de dados TiDB/MySQL...'
php database/migrate.php
php database/create-admin.php

echo '[Meu Canto] A iniciar Apache...'
exec apache2-foreground
