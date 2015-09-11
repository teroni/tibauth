FROM  jeronica/web-base

MAINTAINER Jeronica

RUN rm /etc/nginx/sites-available/default
ADD ./docker-config/default /etc/nginx/sites-available/default

RUN mkdir /var/www/laravel
ADD ./web/ /var/www/laravel

RUN chown www-data -R /var/www/
RUN chmod -R o+w /var/www/laravel/storage
RUN chmod -R o+w /var/www/laravel/public/cache

WORKDIR /var/www/laravel

EXPOSE 80
