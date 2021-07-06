#!/bin/bash

yes | sudo php -d memory_limit=-1 composer.phar install

php artisan optimize:clear
php artisan key:generate
php artisan optimize
php artisan route:clear
php artisan route:cache
php artisan migrate:fresh --seed

ln -s #--SOURCE_FILE--#/public #--SYMBOLIC-LINK--#

sudo find . -type f -exec chmod 644 {} \;
sudo find . -type d -exec chmod 755 {} \;
sudo chmod -R 775 ./storage
sudo chmod -R 775 ./bootstrap/cache/
