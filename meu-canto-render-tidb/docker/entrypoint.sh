#!/bin/sh
set -eu

cd /var/www/html

PORT="${PORT:-10000}"
case "$PORT" in
  ''|*[!0-9]*) echo '[Meu Canto] PORT inválida.' >&2; exit 1 ;;
esac

# Validar estrutura antes de iniciar migrações.
if [ ! -f /var/www/html/public/index.php ]; then
  echo '[Meu Canto] ERRO: /var/www/html/public/index.php não existe.' >&2
  echo '[Meu Canto] Conteúdo de /var/www/html:' >&2
  ls -la /var/www/html >&2
  exit 1
fi

# Render encaminha o tráfego HTTP para a porta indicada em PORT.
sed -ri "s/^Listen 80$/Listen ${PORT}/" /etc/apache2/ports.conf
sed -ri "s/<VirtualHost \\*:80>/<VirtualHost *:${PORT}>/" \
  /etc/apache2/sites-available/000-default.conf

echo "[Meu Canto] PORT=${PORT}"
echo '[Meu Canto] DocumentRoot configurado:'
grep -E '^[[:space:]]*DocumentRoot' /etc/apache2/sites-available/000-default.conf || true

# Confirmar a sintaxe do Apache antes de executar operações de produção.
apache2ctl -t

echo '[Meu Canto] A verificar as três entidades da base de dados...'
php database/migrate.php
php database/create-admin.php

echo '[Meu Canto] A iniciar Apache...'
exec apache2-foreground
