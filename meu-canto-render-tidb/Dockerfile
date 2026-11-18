FROM php:8.2-apache

# Dependências PHP e módulos Apache necessários ao MVC.
RUN apt-get update \
    && apt-get install -y --no-install-recommends ca-certificates libonig-dev \
    && docker-php-ext-install pdo_mysql mysqli mbstring \
    && a2enmod dir rewrite headers expires \
    && rm -rf /var/lib/apt/lists/*

# PHP de produção.
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Apache: servir a aplicação a partir de /public e encaminhar rotas MVC
# (por exemplo /o-que-sinto) para public/index.php.
RUN sed -ri 's#DocumentRoot /var/www/html$#DocumentRoot /var/www/html/public#' \
        /etc/apache2/sites-available/000-default.conf \
    && printf '%s\n' \
        '<Directory /var/www/html/public>' \
        '    Options +FollowSymLinks -Indexes -MultiViews' \
        '    AllowOverride All' \
        '    Require all granted' \
        '    DirectoryIndex index.php index.html' \
        '    FallbackResource /index.php' \
        '</Directory>' \
        > /etc/apache2/conf-available/meu-canto-public.conf \
    && a2enconf meu-canto-public \
    && printf 'ServerName localhost\n' \
        > /etc/apache2/conf-available/meu-canto-servername.conf \
    && a2enconf meu-canto-servername

WORKDIR /var/www/html
COPY . /var/www/html/
COPY docker/entrypoint.sh /usr/local/bin/meu-canto-entrypoint

RUN test -f /var/www/html/public/index.php \
    && mkdir -p /var/www/html/storage/logs \
                /var/www/html/storage/cache \
                /var/www/html/storage/private \
    && chmod +x /usr/local/bin/meu-canto-entrypoint \
    && chown -R www-data:www-data /var/www/html/storage

ENV PORT=10000
EXPOSE 10000
ENTRYPOINT ["meu-canto-entrypoint"]

ENTRYPOINT ["meu-canto-entrypoint"]

