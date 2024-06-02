FROM php:8.1-apache

RUN apt-get update && apt-get install -y supervisor mariadb-client

RUN docker-php-ext-install pdo pdo_mysql

# Imagemagick
RUN apt-get install -y \
        imagemagick \
        ghostscript \
        libmagickwand-dev \
    && rm -rf /var/lib/apt/lists/* \
    && pecl install imagick \
    && docker-php-ext-enable imagick

#ADD /var/www/html/supervisord.conf /etc/supervisor/conf.d/
ADD supervisord.conf /etc/supervisor/conf.d/


CMD ["/usr/bin/supervisord"]
