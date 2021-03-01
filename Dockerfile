#FROM php:7.4-alpine AS Test
#RUN apk add --no-cache composer
#USER 1000:1000
#COPY --chown=1000:1000 ./ /test
#WORKDIR /test
#RUN composer install --no-plugins --no-scripts --no-cache -n -o -v
#RUN ./vendor/bin/phpunit --configuration phpunit.xml

FROM php:7.4-alpine AS Build
RUN apk add --no-cache composer;
WORKDIR /api
RUN chown 1000:1000 .
USER 1000:1000
COPY --chown=1000:1000 src ./src
COPY --chown=1000:1000 public ./public
COPY --chown=1000:1000 ["composer.json", "robots.txt", "Download.php", "./"]
RUN composer install --no-dev --no-plugins --no-scripts --no-cache -n -o -v
RUN rm composer.json

FROM php:7-fpm-alpine AS Run
WORKDIR /api
COPY --from=Build --chown=1000:1000 /api /api
RUN apk add nginx
CMD php-fpm -D; nginx; tail -F /dev/null
