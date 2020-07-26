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
ENV PHP_DISMOD ini_set,php_uname,getmyuid,getmypid,passthru,leak,listen,diskfreespace,tmpfile,link,ignore_user_abord,shell_exec,dl,set_time_limit,exec,system,highlight_file,source,show_source,fpaththru,virtual,posix_ctermid,posix_getcwd,posix_getegid,posix_geteuid,posix_getgid,posix_getgrgid,posix_getgrnam,posix_getgroups,posix_getlogin,posix_getpgid,posix_getpgrp,posix_getpid,posix,_getppid,posix_getpwnam,posix_getpwuid,posix_getrlimit,posix_getsid,posix_getuid,posix_isatty,posix_kill,posix_mkfifo,posix_setegid,posix_seteuid,posix_setgid,posix_setpgid,posix_setsid,posix_setuid,posix_times,posix_ttyname,posix_uname,proc_open,proc_close,proc_get_status,proc_nice,proc_terminate,phpinfo,popen,curl_multi_exec,parse_ini_file,allow_url_fopen,allow_url_include,pcntl_exec,chgrp,chmod,chown,lchgrp,lchown,putenv
COPY robots.txt ./
RUN pwd
RUN composer install --no-dev
RUN chown application:application -R /app/
