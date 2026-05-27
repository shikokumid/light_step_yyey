FROM php:8.2-apache
RUN a2dismod mpm_event mpm_worker || true && a2enmod mpm_prefork
RUN docker-php-ext-install pdo_mysql mysqli
COPY . /var/www/html/
EXPOSE 80
RUN a2dismod mpm_event mpm_worker && a2enmod mpm_prefork
