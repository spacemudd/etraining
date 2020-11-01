#!/bin/sh

cp /etc/supervisor/conf.d/horizon.conf.tpl /etc/supervisor/supervisord.conf

supervisord --nodaemon --configuration /etc/supervisor/supervisord.conf
