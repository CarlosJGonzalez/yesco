FROM php:7.4.10-apache AS final
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY ./src /var/www/html
USER www-data

FROM php:7.4.10-apache AS final

# Install necessary extensions
RUN apt-get update && apt-get install -y \
    libpq-dev libzip-dev unzip curl git \
    && mysql-client

# Copy application files
WORKDIR /var/www/html
COPY ./src /var/www/html

# Set permissions
RUN chown -R www-data:www-data /var/www/html

# Configure Apache
RUN a2enmod rewrite

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Start Apache
CMD ["apache2-foreground"]