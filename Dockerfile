# Use the official PHP 8.3 image with Apache
FROM php:8.3-apache

COPY ./composer.json /var/www/html/

# Install composer
RUN curl -sS https://getcomposer.org/installer | php

# Installs Composer after downloading the PHAR
RUN php composer.phar install

# Enables the rewrite module for .htaccess
RUN a2enmod rewrite

# Create a standalone release package
#RUN tar -czvf gio-php-v$(cat VERSION).tar.gz src/

# ll alias
#RUN alias ll="ls -l"

# Expose port 80
EXPOSE 80