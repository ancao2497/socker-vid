#!/bin/sh
set -e
#ls -al /var/www/
bash
php artisan reverb:start

exec "$@"