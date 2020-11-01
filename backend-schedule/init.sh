#!/bin/bash

while [ true ]
do
  php /usr/src/app/artisan schedule:run
  sleep 60
done
