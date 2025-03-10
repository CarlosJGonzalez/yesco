FROM php:7.4-apache AS final

# Install necessary extensions
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli
RUN a2enmod rewrite

# Copy application files
COPY ./src /var/www/html/
COPY ./local/etc/php /usr/local/etc/php/

# Enable short_open_tag
RUN echo "short_open_tag=On"

WORKDIR /var/www/html
RUN chmod -R 755 /uploads && chown -R www-data:www-data /uploads
RUN chown -R 755 /img && chown -R www-data:www-data /img

# Expose port 80 for web traffic 
EXPOSE 80