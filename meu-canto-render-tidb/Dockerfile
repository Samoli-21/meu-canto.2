FROM php:8.2-apache

# Dependências do PHP e módulos Apache necessários ao MVC.
RUN apt-get update \
    && apt-get install -y --no-install-recommends ca-certificates libonig-dev \
    && docker-php-ext-install pdo_mysql mysqli mbstring \
    && a2enmod rewrite headers expires \
    && rm -rf /var/lib/apt/lists/*

# Configuração PHP de produção.
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# IMPORTANTE: definir o DocumentRoot de forma explícita.
# Evita depender da expansão de APACHE_DOCUMENT_ROOT dentro do VirtualHost.
RUN sed -ri 's#DocumentRoot /var/www/html$#DocumentRoot /var/www/html/public#' \
        /etc/apache2/sites-available/000-default.conf \
    && printf '<Directory /var/www/html/public>\n    Options FollowSymLinks\n    AllowOverride All\n    Require all granted\n</Directory>\nDirectoryIndex index.php index.html\n' \
        > /etc/apache2/conf-available/meu-canto-public.conf \
    && a2enconf meu-canto-public \
    && printf 'ServerName localhost\n' > /etc/apache2/conf-available/meu-canto-servername.conf \
    && a2enconf meu-canto-servername

WORKDIR /var/www/html
COPY . /var/www/html/
COPY docker/entrypoint.sh /usr/local/bin/meu-canto-entrypoint

# Falhar no build, e não em produção, se faltar o ponto de entrada público.
RUN test -f /var/www/html/public/index.php \
    && test -f /var/www/html/public/.htaccess \
    && mkdir -p /var/www/html/storage/logs \
                /var/www/html/storage/cache \
                /var/www/html/storage/private \
    && chmod +x /usr/local/bin/meu-canto-entrypoint \
    && chown -R www-data:www-data /var/www/html/storage

ENV PORT=10000
EXPOSE 10000
ENTRYPOINT ["meu-canto-entrypoint"]
