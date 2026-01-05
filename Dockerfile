# Use the official PHP 8.3 image with Apache
FROM php:8.3-apache

#COPY ./composer.json /var/www/html/

# Downloads Composer
#RUN curl -sS https://getcomposer.org/installer | php

# Install Composer dependencies
#RUN php composer.phar install

# Enables the rewrite module for .htaccess
RUN a2enmod rewrite

# Expose port 80
EXPOSE 80