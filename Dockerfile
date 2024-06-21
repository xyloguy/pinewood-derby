FROM php:8-apache
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli
RUN a2enmod ssl
CMD ["apache2-foreground"]
