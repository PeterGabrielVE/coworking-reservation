FROM php:8.1-apache

# 1. Instalar dependencias del sistema y Node.js LTS
RUN apt-get update && \
    apt-get install -y \
    openssl \
    zip \
    unzip \
    git \
    sqlite3 \
    gnupg \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    # Install Node.js and npm properly
    && curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs \
    # Verify installation
    && node --version \
    && npm --version \
    # Clean up
    && rm -rf /var/lib/apt/lists/* \
    # Install additional build tools if needed
    && apt-get update && apt-get install -y build-essential

# 2. Verificar versiones instaladas
RUN node --version && npm --version

# 3. Instalar extensiones PHP
RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

# 4. Configurar Apache
RUN a2enmod rewrite
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Instalar Composer (versión específica para PHP 8.1)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer --version=2.2.18

# 6. Configurar directorio y permisos
RUN mkdir -p /var/www/html \
    && chown -R www-data:www-data /var/www/html

WORKDIR /var/www/html
