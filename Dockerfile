# Use the official PHP 8.3 image with Apache
FROM php:8.3-apache

# Downloads Composer
#RUN curl -sS https://getcomposer.org/installer | php

# Install Composer dependencies
#RUN php composer.phar install

# Create logs folder
RUN mkdir /var/www/html/log && chown www-data:www-data /var/www/html/log

# Enables the rewrite module for .htaccess
RUN a2enmod rewrite

# Expose port 80
EXPOSE 80