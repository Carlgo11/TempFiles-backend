FROM webdevops/php-nginx:7.4-alpine
WORKDIR /app
COPY src ./src
COPY composer.* ./
COPY *.php ./
ENV FPM_PM_START_SERVERS 2
ENV FPM_PM_MIN_SPARE_SERVERS 1
ENV FPM_PM_MAX_SPARE_SERVERS 3
ENV FPM_PM_MAX_CHILDREN 5
ENV PHP_MAX_EXECUTION_TIME 60
COPY robots.txt ./
RUN pwd
RUN composer install
RUN chown application:application -R /app/
