
FROM php:8.2-apache

# Instalar dependências e extensões PHP
RUN apt-get update \
    && apt-get install -y --no-install-recommends ca-certificates libonig-dev \
    && docker-php-ext-install pdo_mysql mysqli mbstring \
    && a2enmod rewrite headers expires \
    && rm -rf /var/lib/apt/lists/*

# Configuração PHP para produção
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Configuração do Apache
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && printf '<Directory /var/www/html/public>\nAllowOverride All\nRequire all granted\n</Directory>\n' \
    > /etc/apache2/conf-available/meu-canto-public.conf \
    && a2enconf meu-canto-public

# Diretório da aplicação
WORKDIR /var/www/html

COPY . /var/www/html/

# Script de inicialização
COPY docker/entrypoint.sh /usr/local/bin/meu-canto-entrypoint

# CRIAR AS PASTAS ANTES DE ALTERAR AS PERMISSÕES
RUN mkdir -p /var/www/html/storage/logs \
             /var/www/html/storage/cache \
             /var/www/html/storage/private \
    && chmod +x /usr/local/bin/meu-canto-entrypoint \
    && chown -R www-data:www-data /var/www/html/storage

# Porta utilizada pelo Render
ENV PORT=10000
EXPOSE 10000

ENTRYPOINT ["meu-canto-entrypoint"]

EXPOSE 10000
ENTRYPOINT ["meu-canto-entrypoint"]
