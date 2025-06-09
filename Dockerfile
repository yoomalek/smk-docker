FROM php:7.4-apache

RUN docker-php-ext-install mysqli
RUN a2enmod rewrite

COPY ./app /var/www/html/
RUN chown -R www-data:www-data /var/www/html/

