# Meu Canto — PHP 8.2 + Apache para Render e TiDB Cloud
FROM php:8.2-apache

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public \
    PORT=10000

# Extensões necessárias ao PDO MySQL, formulários e texto em português.
RUN apt-get update \
    && apt-get install -y --no-install-recommends ca-certificates libonig-dev \
    && docker-php-ext-install pdo_mysql mysqli mbstring \
    && a2enmod rewrite dir headers expires \
    && rm -rf /var/lib/apt/lists/*

# Configuração PHP de produção.
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && printf '%s\n' \
       'expose_php=Off' \
       'display_errors=Off' \
       'log_errors=On' \
       'memory_limit=256M' \
       'upload_max_filesize=10M' \
       'post_max_size=12M' \
       'max_execution_time=60' \
       > "$PHP_INI_DIR/conf.d/meu-canto.ini"

# Apache serve exclusivamente a pasta public/.
RUN sed -ri 's#DocumentRoot /var/www/html#DocumentRoot /var/www/html/public#g' \
       /etc/apache2/sites-available/*.conf \
    && printf '%s\n' \
       'ServerName localhost' \
       '<Directory /var/www/html/public>' \
       '    Options +FollowSymLinks -Indexes -MultiViews' \
       '    AllowOverride All' \
       '    Require all granted' \
       '    DirectoryIndex index.php' \
       '    FallbackResource /index.php' \
       '</Directory>' \
       > /etc/apache2/conf-available/meu-canto-public.conf \
    && a2enconf meu-canto-public

WORKDIR /var/www/html
COPY . /var/www/html/
COPY docker/entrypoint.sh /usr/local/bin/meu-canto-entrypoint

# Falhar durante o build se o projecto estiver incompleto.
RUN test -f /var/www/html/public/index.php \
    && test -f /var/www/html/public/.htaccess \
    && test -f /var/www/html/routes/web.php \
    && mkdir -p /var/www/html/storage/logs \
                /var/www/html/storage/cache \
                /var/www/html/storage/private/documents \
                /var/www/html/storage/sessions \
    && chmod +x /usr/local/bin/meu-canto-entrypoint \
    && chown -R www-data:www-data /var/www/html/storage

EXPOSE 10000
ENTRYPOINT ["meu-canto-entrypoint"]
