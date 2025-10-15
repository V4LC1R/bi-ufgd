# Base image
FROM php:8.4-apache

# Argumentos para receber o ID do usuário e do grupo da sua máquina
# O valor padrão 1000 é usado se nada for passado
ARG UID=1000
ARG GID=1000

# Set working directory
WORKDIR /var/www/html

# Instalar dependências e extensões PHP em uma única camada para otimização
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libxml2-dev \
    libpq-dev \
    zip unzip git curl vim locales \
    jpegoptim optipng pngquant gifsicle \
    && apt-get clean && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install pcntl bcmath exif soap pdo pdo_mysql pdo_pgsql pgsql \
    && docker-php-ext-configure gd --enable-gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ \
    && docker-php-ext-install gd \
    && pecl install redis && docker-php-ext-enable redis

# Instalar XDebug
RUN yes | pecl install xdebug \
    && echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/xdebug.ini

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# --- A MÁGICA PARA RESOLVER AS PERMISSÕES ---
# Modifica o usuário 'www-data' existente (que o Apache usa)
# para ter o mesmo ID do seu usuário na máquina host.
RUN groupmod -g ${GID} www-data \
    && usermod -u ${UID} -g www-data www-data

# Habilitar mod_rewrite do Apache
RUN a2enmod rewrite

# Copiar configs PHP (assumindo que estão no mesmo diretório do Dockerfile)
COPY local.ini /usr/local/etc/php/

# Expor porta do Apache
EXPOSE 80

# Define o usuário padrão do contêiner como www-data.
# Como já ajustamos o ID dele, tudo funcionará perfeitamente.
USER www-data