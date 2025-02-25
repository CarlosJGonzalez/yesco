FROM php:7.4.10-apache AS final
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY ./src /var/www/html
USER www-data