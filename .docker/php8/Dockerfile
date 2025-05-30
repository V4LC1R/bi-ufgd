FROM --platform=linux/amd64 php:8.4-apache

# Set working directory
WORKDIR /var/www/html

# Install dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libmcrypt-dev \
    build-essential \
    libpq-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libxml2-dev

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install extensions
RUN docker-php-ext-install exif pcntl soap bcmath

# Install Postgre PDO
RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql pgsql \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql

RUN docker-php-ext-configure gd --enable-gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/
RUN docker-php-ext-install gd

RUN pecl install redis && docker-php-ext-enable redis

# Install XDebug
RUN yes | pecl install xdebug \
    && echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_enable=on" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_autostart=off" >> /usr/local/etc/php/conf.d/xdebug.ini

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer self-update 2.4.1

# Add user for laravel application
RUN groupadd -g 1000 ubuntu
RUN useradd -u 1000 -ms /bin/bash -g ubuntu ubuntu

# enable mod_rewrite
RUN a2enmod rewrite
RUN a2enmod include

# Use the default production configuration
COPY php.ini /usr/local/etc/php/
COPY local.ini /usr/local/etc/php/

# Change current user to www
# USER ubuntu
