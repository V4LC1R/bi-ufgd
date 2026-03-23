# Base image (Debian-based já)
FROM php:8.4-apache

# Args de usuário
ARG UID=1000
ARG GID=1000

# Define bash como shell padrão
SHELL ["/bin/bash", "-c"]

# Diretório da app
WORKDIR /var/www/html

# Variáveis de ambiente importantes pro terminal
ENV TERM=xterm-256color
ENV HOME=/var/www

# Instala dependências + bash completo
RUN apt-get update && apt-get install -y \
    bash \
    bash-completion \
    readline-common \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libxml2-dev \
    libpq-dev \
    zip unzip git curl vim locales \
    jpegoptim optipng pngquant gifsicle \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Extensões PHP
RUN docker-php-ext-install \
    pcntl bcmath exif soap \
    pdo pdo_mysql pdo_pgsql pgsql

# GD
RUN docker-php-ext-configure gd \
    --enable-gd \
    --with-freetype=/usr/include/ \
    --with-jpeg=/usr/include/ \
    && docker-php-ext-install gd

# Redis
RUN pecl install redis && docker-php-ext-enable redis

# Xdebug
RUN yes | pecl install xdebug \
    && echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/xdebug.ini

# Composer
RUN curl -sS https://getcomposer.org/installer | php \
    -- --install-dir=/usr/local/bin --filename=composer

# Ajusta usuário www-data pro host
RUN groupmod -g ${GID} www-data \
    && usermod -u ${UID} -g www-data www-data

# Cria HOME funcional (resolve histórico)
RUN mkdir -p /var/www \
    && chown -R www-data:www-data /var/www

# Bash config (histórico + autocomplete)
RUN echo "export HISTFILE=/var/www/.bash_history" >> /etc/bash.bashrc && \
    echo "export HISTSIZE=10000" >> /etc/bash.bashrc && \
    echo "export HISTFILESIZE=20000" >> /etc/bash.bashrc && \
    echo "bind '\"\\e[A\": history-search-backward'" >> /etc/bash.bashrc && \
    echo "bind '\"\\e[B\": history-search-forward'" >> /etc/bash.bashrc

# Apache
RUN a2enmod rewrite

# PHP config
COPY local.ini /usr/local/etc/php/

# Porta
EXPOSE 80