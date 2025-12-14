# Dockerfile - PHP 8.2 Apache with PDO/MySQL and MongoDB extension
FROM php:8.2-apache

# Install system deps needed to compile extensions & tools
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libssl-dev \
    pkg-config \
    libcurl4-openssl-dev \
    wget \
    build-essential \
    default-mysql-client \
    && rm -rf /var/lib/apt/lists/*

# Enable apache rewrite and required Apache configs
RUN a2enmod rewrite

# Install PHP extensions: pdo, pdo_mysql, mbstring, xml, curl
RUN docker-php-ext-install pdo pdo_mysql mbstring xml curl

# Install libs for pecl (to install mongodb)
RUN apt-get update && apt-get install -y libgssapi-krb5-2 gnupg ca-certificates

# Install PECL and install mongodb extension
RUN pecl channel-update pecl.php.net \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb

# Install Composer (global)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy project files
WORKDIR /var/www/html
COPY . /var/www/html

# Ensure proper permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Install PHP dependencies using Composer
RUN composer install --no-dev --optimize-autoloader

# If your app needs a writable folder for uploads / cache, create it:
RUN mkdir -p /var/www/html/storage && chown -R www-data:www-data /var/www/html/storage

# Expose port 80 and start Apache
EXPOSE 80
CMD ["apache2-foreground"]