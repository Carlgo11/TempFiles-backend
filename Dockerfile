FROM php:7.4-alpine AS Test
RUN apk add --no-cache composer
USER 1000:1000
COPY --chown=1000:1000 ./ /test
WORKDIR /test
RUN composer install --no-plugins --no-scripts --no-cache -n -o -v
RUN ./vendor/bin/phpunit --configuration phpunit.xml

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

FROM webdevops/php-nginx:7.4-alpine AS Run
WORKDIR /api
COPY --from=Build --chown=1000:1000 /api /api

ENV FPM_PM_START_SERVERS 2
ENV FPM_PM_MIN_SPARE_SERVERS 1
ENV FPM_PM_MAX_SPARE_SERVERS 3
ENV FPM_PM_MAX_CHILDREN 5
ENV PHP_MAX_EXECUTION_TIME 60
ENV PHP_DISMOD ini_set,php_uname,getmyuid,getmypid,passthru,leak,listen,diskfreespace,tmpfile,link,ignore_user_abord,shell_exec,dl,set_time_limit,exec,system,highlight_file,source,show_source,fpaththru,virtual,posix_ctermid,posix_getcwd,posix_getegid,posix_geteuid,posix_getgid,posix_getgrgid,posix_getgrnam,posix_getgroups,posix_getlogin,posix_getpgid,posix_getpgrp,posix_getpid,posix,_getppid,posix_getpwnam,posix_getpwuid,posix_getrlimit,posix_getsid,posix_getuid,posix_isatty,posix_kill,posix_mkfifo,posix_setegid,posix_seteuid,posix_setgid,posix_setpgid,posix_setsid,posix_setuid,posix_times,posix_ttyname,posix_uname,proc_open,proc_close,proc_get_status,proc_nice,proc_terminate,phpinfo,popen,curl_multi_exec,parse_ini_file,allow_url_fopen,allow_url_include,pcntl_exec,chgrp,chmod,chown,lchgrp,lchown,putenv
