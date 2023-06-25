FROM php:8.1-apache

RUN apt-get update && apt-get install -y supervisor

#ADD /var/www/html/supervisord.conf /etc/supervisor/conf.d/
ADD supervisord.conf /etc/supervisor/conf.d/


CMD ["/usr/bin/supervisord"]
