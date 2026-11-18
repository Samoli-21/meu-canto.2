
#!/bin/sh
set -eu

cd /var/www/html

PORT="${PORT:-10000}"

case "$PORT" in
  ''|*[!0-9]*)
    echo '[Meu Canto] ERRO: PORT inválida.' >&2
    exit 1
    ;;
esac

# Validar os ficheiros principais
if [ ! -f /var/www/html/public/index.php ] || \
   [ ! -f /var/www/html/routes/web.php ]; then
    echo '[Meu Canto] ERRO: estrutura MVC incompleta.' >&2
    exit 1
fi

# Configurar Apache para a porta do Render
sed -ri "s/^Listen 80$/Listen ${PORT}/" \
    /etc/apache2/ports.conf

sed -ri "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/" \
    /etc/apache2/sites-available/000-default.conf

echo "[Meu Canto] Porta configurada: ${PORT}"

# Validar Apache
apache2ctl -t

# Migrar banco de dados
echo '[Meu Canto] A verificar a base de dados...'
php database/migrate.php

# Criar administrador, caso ainda não exista
php database/create-admin.php

# Iniciar servidor
echo '[Meu Canto] A iniciar Apache...'
exec apache2-foreground

