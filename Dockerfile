
FROM php:8.2-apache

ENV PORT=10000

# Dependências e extensões PHP
RUN apt-get update \
    && apt-get install -y --no-install-recommends ca-certificates libonig-dev \
    && docker-php-ext-install pdo_mysql mysqli mbstring \
    && a2enmod dir rewrite headers expires \
    && rm -rf /var/lib/apt/lists/*

# Configuração PHP de produção
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Configuração do Apache e pasta pública
RUN sed -ri 's#DocumentRoot /var/www/html#DocumentRoot /var/www/html/public#' \
    /etc/apache2/sites-available/000-default.conf \
    && printf '%s\n' \
       'ServerName localhost' \
       '<Directory /var/www/html/public>' \
       '    Options +FollowSymLinks -Indexes -MultiViews' \
       '    AllowOverride All' \
       '    Require all granted' \
       '    DirectoryIndex index.php index.html' \
       '    FallbackResource /index.php' \
       '</Directory>' \
       > /etc/apache2/conf-available/meu-canto-public.conf \
    && a2enconf meu-canto-public \
    && apache2ctl configtest

WORKDIR /var/www/html

# Copiar a aplicação
COPY . /var/www/html/

# Script de inicialização
COPY docker/entrypoint.sh /usr/local/bin/meu-canto-entrypoint

# Criar pastas necessárias e configurar permissões
RUN test -f /var/www/html/public/index.php \
    && mkdir -p /var/www/html/storage/logs \
                /var/www/html/storage/cache \
                /var/www/html/storage/private \
    && chmod +x /usr/local/bin/meu-canto-entrypoint \
    && chown -R www-data:www-data /var/www/html/storage

# Porta do Render
EXPOSE 10000

# Executar o script de inicialização
ENTRYPOINT ["meu-canto-entrypoint"]
