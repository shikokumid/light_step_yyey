FROM php:8.2-apache

RUN docker-php-ext-install pdo_mysql mysqli

COPY . /var/www/html/

RUN sed -i 's/80/${PORT}/g' /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf

CMD sh -c "sed -i \"s/\\\${PORT}/${PORT}/g\" /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf && apache2-foreground"
