FROM php:7.4-apache

RUN apt-get update && apt-get install -y \
            libfreetype6-dev \
            libpng-dev \
            libwebp-dev \
            libjpeg62-turbo-dev \
            libmcrypt-dev \
            libzip-dev \
            zip \
            git \
            mariadb-client \
    && docker-php-ext-install \
        pdo_mysql \
        gd \
        zip \
        && a2enmod \
        rewrite

# Add the user UID:1000, GID:1000, home at /app
#RUN groupadd -r app -g 1000 && useradd -u 1000 -r -g app -m -d /app -s /sbin/nologin -c "App user" app && \
#    chmod 755 /var/www/html
RUN usermod -u 1000 www-data && groupmod -g 1000 www-data
RUN chmod 755 /var/www/html

RUN php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer

#upload
RUN echo "file_uploads = On\n" \
         "memory_limit = 500M\n" \
         "upload_max_filesize = 500M\n" \
         "post_max_size = 500M\n" \
         "max_execution_time = 600\n" \
         > /usr/local/etc/php/conf.d/uploads.ini

#USER www-data

WORKDIR /var/www/html

USER root

COPY vhost.conf /etc/apache2/sites-enabled/000-default.conf

CMD ["/usr/sbin/apache2ctl", "-D", "FOREGROUND"]

# Install imagemagick, python, pip, and pillow
RUN apt-get update -y
RUN apt-get install imagemagick -y
RUN apt-get install -y python3 python3-pip
# install pip dependencies
RUN apt-get install libjpeg-dev zlib1g-dev -y
RUN python3 -m pip install pillow

EXPOSE 80
