# Base image
FROM php:8.4-apache

# Set working directory
WORKDIR /var/www/html

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libxml2-dev \
    libpq-dev \
    zip unzip git curl vim locales \
    jpegoptim optipng pngquant gifsicle \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Habilitar extensões PHP
RUN docker-php-ext-install \
    pcntl \
    bcmath \
    exif \
    soap \
    pdo \
    pdo_mysql \
    pdo_pgsql \
    pgsql \
    && docker-php-ext-configure gd --enable-gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ \
    && docker-php-ext-install gd

# Instalar Redis
RUN pecl install redis && docker-php-ext-enable redis

# Instalar XDebug
RUN yes | pecl install xdebug \
    && echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/xdebug.ini

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer self-update 2.4.1

# Adicionar usuário para Laravel
RUN groupadd -g 1000 ubuntu \
    && useradd -u 1000 -ms /bin/bash -g ubuntu ubuntu

# Habilitar mod_rewrite do Apache
RUN a2enmod rewrite include

# Copiar configs PHP
COPY php.ini /usr/local/etc/php/
COPY local.ini /usr/local/etc/php/

# Ajustar permissões (opcional)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Expor porta do Apache
EXPOSE 80

# Usar usuário www-data
USER www-data