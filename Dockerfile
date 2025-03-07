FROM php:7.4-apache
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Install necessary extensions
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Copy application files
COPY ./src /var/www/html/

# Expose port 80 for web traffic 
EXPOSE 80