FROM php:7.4-apache
COPY --from=composer /usr/bin/composer /usr/bin/composer
