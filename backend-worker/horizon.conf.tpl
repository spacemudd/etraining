[supervisord]
nodaemon=true

[program:horizon]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/artisan horizon
autostart=true
autorestart=true
numprocs=1
startretries=10
redirect_stderr=true
stdout_events_enabled=1
logfile_maxbytes=50MB
stdout_logfile=/var/www/horizon.log
